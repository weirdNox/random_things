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

if [[ -f PKGBUILD ]]
then
    . PKGBUILD
    Acer_pkgver=$pkgver
    Acer_pkgrel=$pkgrel
else
    Acer_pkgver="NONE"
    Acer_pkgrel="0"
fi

ArchVer="$Arch_pkgver-$Arch_pkgrel"
AcerVer="$Acer_pkgver-$Acer_pkgrel"

if [[ "$AcerVer" == "$ArchVer" ]]
then
    echo "Already have the latest version ($AcerVer)"
else
    echo "Rebuilding package ($AcerVer -> $ArchVer)"

    # NOTE(nox): Fetch the kernel config from the Arch version
    cp linux/config config

    # NOTE(nox): Generate PKGBUILD for building the custom kernel
    rm -f PKGBUILD
    cp PKGBUILD.acer PKGBUILD
    sed -i '/^pkgver=$/ s/$/'"$Arch_pkgver"'/' PKGBUILD
    sed -i '/^pkgrel=$/ s/$/'"$Arch_pkgrel"'/' PKGBUILD

    # NOTE(nox): Build and package the kernel
    makepkg -s
    Package="linux-$ArchVer-x86_64.pkg.tar.zst"

    if [[ -f "$Package" ]]
    then
        echo "The new kernel has been successfully packaged in $Package"
        chmod 644 "$Package"

        echo "Adding $Package to the repository..."
        sudo mv "$Package" "$RepoFolder/"
        cd "$RepoFolder"
        sudo repo-add --new --remove "$RepoFile" "$Package"

        echo "Done!"
    else
        echo "Package $Package was _not_ created... :("
        exit 1
    fi
fi
