#!/usr/bin/bash
set -euo pipefail; shopt -s nullglob
script_dir="$(dirname -- "${BASH_SOURCE[0]}")"
cd -- "$script_dir" >/dev/null 2>&1

# ====================================================================================================
# CONFIGURATION
declare -A default_config=(
    [repo]="/srv/http/repo.db.tar.zst"
    [acer_ver]="NONE"
)
for var in "${!default_config[@]}"; do declare "$var"="${default_config[$var]}"; done

config_file="builder.cfg"
[[ -f "$config_file" ]] && source "$config_file"

# ====================================================================================================
# BUILD
[[ ! -d linux ]] && pkgctl repo clone --protocol=https linux >/dev/null 2>&1
(cd linux && git clean -fxd && git pull) >/dev/null

. linux/PKGBUILD
arch_pkgver="$pkgver"
arch_pkgrel="$pkgrel"
arch_extractdir="$_srcname"

arch_ver="$arch_pkgver-$arch_pkgrel"

if [[ "$acer_ver" == "$arch_ver" ]]; then
    echo "Already have the latest version ($acer_ver)"
else
    echo "Rebuilding package ($acer_ver -> $arch_ver)"

    # NOTE(nox): Update config
    acer_ver="$arch_ver"
    rm -f "$config_file" && for var in "${!default_config[@]}"; do declare -p "$var" >> "$config_file"; done

    # NOTE(nox): Patch and build the kernel
    cd linux
    makepkg --nobuild --skippgpcheck --syncdeps --noconfirm
    sed -i 's/Switch SA5-271/SA5-271/' "src/$arch_extractdir/drivers/ata/ahci.c"
    makepkg --noextract

    package="linux-$arch_ver-x86_64.pkg.tar.zst"

    # NOTE(nox): Add it to the repo
    if [[ -f "$package" ]]; then
        echo "Adding '$package' to the repository (at '$repo')..."
        repo_folder="$(dirname -- "$repo")"
        chmod 644 "$package" && sudo mv "$package" "$repo_folder/"
        sudo repo-add --new --remove "$repo" "$repo_folder/$package"
    else
        echo "Package '$package' was _not_ created... :("
        exit 1
    fi
fi
