#!/usr/bin/bash
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1

# ====================================================================================================
# CONFIGURATION
RepoFolder="/srv/http"
RepoFile=repo.db.tar.zst

# ====================================================================================================
# BUILD
rm -rf linux
asp update linux >/dev/null 2>&1
asp export linux >/dev/null

. linux/PKGBUILD
Arch_pkgver=$pkgver
Arch_pkgrel=$pkgrel
ArchVer="$Arch_pkgver-$Arch_pkgrel"

if [[ -f PKGBUILD ]]
then
    . PKGBUILD
    Local_pkgver=$pkgver
    Local_pkgrel=$pkgrel
    LocalVer="$Local_pkgver-$Local_pkgrel"
else
    Local_pkgver="NONE"
    Local_pkgrel=
    LocalVer="$Local_pkgver"
fi

if [[ "$ArchVer" == "$LocalVer" ]]
then
    echo "Already have the latest version ($LocalVer)"
else
    echo "Rebuilding package ($LocalVer -> $ArchVer)"

    cp linux/config config

    echo "Downloading official Arch kernel package..."
    wget 'https://archlinux.org/packages/testing/x86_64/linux/download/'  -O linux.pkg.tar.zst >/dev/null 2>&1 || \
        wget 'https://archlinux.org/packages/core/x86_64/linux/download/' -O linux.pkg.tar.zst >/dev/null 2>&1

    # NOTE(nox): Check if the downloaded package is the latest one
    pacman -Qip linux.pkg.tar.zst | grep -E 'Version.*:.*'"$ArchVer" || \
        { echo "Downloaded package is not the latest!"; exit 1; }

    rm -f PKGBUILD
    cp PKGBUILD.orig PKGBUILD
    sed -i '/^pkgver=$/ s/$/'"$Arch_pkgver"'/' PKGBUILD
    sed -i '/^pkgrel=$/ s/$/'"$Arch_pkgrel"'/' PKGBUILD

    echo "Building package"
    rm -rf src/usr # NOTE(nox): Remove old packages contents
    MAKEFLAGS="-j$(nproc)" makepkg -s

    Package="linux-$ArchVer-x86_64.pkg.tar.zst"

    if [[ -f "$Package" ]]
    then
        echo "The new kernel has been successfully packaged in $Package"
        chmod 644 "$Package"

        echo "Adding $Package to the repository..."
        sudo mv "$Package" "$RepoFolder/"
        cd "$RepoFolder"
        sudo repo-add --new --remove "$RepoFile" "$Package"
    else
        echo "Package $Package was _not_ created... :("
        exit 1
    fi
fi
