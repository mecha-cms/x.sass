SCSS Extension for [Mecha](https://github.com/mecha-cms/mecha)
==============================================================

![Code Size](https://img.shields.io/github/languages/code-size/mecha-cms/x.scss?color=%23444&style=for-the-badge)

SASS is a style sheet language initially designed by [Hampton Catlin](https://en.wikipedia.org/wiki/Hampton_Catlin) and developed by Natalie Weizenbaum. After its initial versions, Weizenbaum and Chris Eppstein have continued to extend SASS with SassScript, a simple scripting language used in SASS files. SASS is a preprocessor scripting language that is interpreted or compiled into Cascading Style Sheets (CSS). SassScript is the scripting language itself. SASS consists of two syntaxes. The original syntax, called “the indented syntax,” uses a syntax similar to [HAML](https://en.wikipedia.org/wiki/Haml). It uses indentation to separate code blocks and newline characters to separate rules. The newer syntax, SCSS, uses block formatting like that of CSS. It uses braces to denote code blocks and semicolons to separate lines within a block. The indented syntax and SCSS files are traditionally given the extensions `.sass` and `.scss`, respectively.

This extension compiles SCSS code into CSS code by looking for files with extension `.scss` added through the `Asset::set()` method, storing the compiled results as static files and then displays them as CSS files. The compiled file contents will be updated automatically on every file modification changes on the SCSS files.

---

Release Notes
-------------

### 1.4.0

 - Moved `vendor` folder to the extension root.
 - [@mecha-cms/mecha#96](https://github.com/mecha-cms/mecha/issues/96)

### 1.3.2

 - Changed third party directory pattern to `./lot/x/:extension/engine/i/:user/:repo` for consistency.

### 1.3.1

 - Updated SCSS library to `1.0.6`.
 - Removed `mecha-cms/extend.plugin.scss` repository. Art direction with SCSS syntax is available by default.