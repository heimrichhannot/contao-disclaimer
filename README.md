# Disclaimer

Disclaimer brings a backend and frontend module to contao. If the user for example tries to download a file, he first has to accept a disclaimer.

## Features

- Protect file download by adding a disclaimer
- Multiple language support (depending on available page languages)
- Extendable by several hooks
- Works perfectly together with `heimrichhannot/contao-modal`, just install it and display disclaimers within modal windows.

### Hooks

Name | Arguments | Expected return value | Description
 ---------- | ---------- | ---------- | ---------
showDisclaimer | $objDisclaimer, $blnAccepted | false if should break, otherwise void | Add custom showDisclaimer logic. (For example see: `heimrichhannot/contao-modal`).
getDisclaimerSourceOptions | $arrOptions, $dc | $arrOptions | Add custom source options to tl_disclaimer. (For example see: `heimrichhannot/contao-modal`).

