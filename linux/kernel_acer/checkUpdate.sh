#!/usr/bin/bash
set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1

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
    wget 'https://archlinux.org/packages/core/x86_64/linux/download/' -O linux.pkg.tar.zst >/dev/null 2>&1

    rm -f PKGBUILD
    cp PKGBUILD.orig PKGBUILD
    sed -i '/^pkgver=$/ s/$/'"$Arch_pkgver"'/' PKGBUILD
    sed -i '/^pkgrel=$/ s/$/'"$Arch_pkgrel"'/' PKGBUILD

    PackageWildcard=linux-*.pkg.tar.zst

    if ! compgen -G $PackageWildcard
    then
        echo "Building package"

        MAKEFLAGS="-j$(nproc)" makepkg -s
    else
        echo "Package already exists, skipping..."
    fi

    chmod 644 $PackageWildcard

    # echo "Adding the new kernel to the repository..."
    # RepoFolder="/srv/http"
    # RepoFile=repo.db.tar.zst
    # sudo repo-add --new --remove "$RepoFolder/$RepoFile" $PackageWildcard
    # sudo mv $PackageWildcard "$RepoFolder/"
fi
