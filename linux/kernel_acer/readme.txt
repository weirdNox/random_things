The Acer Switch Alpha 12 (SA5-271) 2-in-1 tablet PC needs a special
workaround for its internal SSD to be found by the kernel.

This workaround is already present in the kernel.

However, the kernel identifies the device by its DMI codes, which
don't seem to be consistent throughout all units.

This script recompiles only the latest, patched, bzImage (vmlinuz) and
then packages it, together with the modules from the official Linux
kernel Arch package.
