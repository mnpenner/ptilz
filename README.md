# Ptilz #

A collection of general-purpose PHP utility methods.

Ptilz is currently pre-1.0 and as such should be considered to have an unstable API. Be prepared for things to be refactored without warning.

I aim to provide full test coverage of every function, but I'm not quite there yet.

Most functions have doc-comments; read them! If usage still isn't clear check out the unit tests in the `tests/` folder.

## Documentation ##

Static utility classes:

- [`\Ptilz\Arr`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Arr.php) - Array functions
- [`\Ptilz\Bin`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Bin.php) - Binary functions
- [`\Ptilz\Cli`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Cli.php) - Command-line/console functions
- [`\Ptilz\Dbg`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Dbg.php) - Debug
- [`\Ptilz\Env`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Env.php) - Environment
- [`\Ptilz\Func`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Func.php) - Meta functions
- [`\Ptilz\Html`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Html.php) - HTML
- [`\Ptilz\Iter`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Iter.php) - Iterable/traversable/generator stuff
- [`\Ptilz\Json`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Json.php) - JSON
- [`\Ptilz\Math`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Math.php) - Math
- [`\Ptilz\Path`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Path.php) - File paths (inspired by [Node](http://nodejs.org/api/path.html))
- [`\Ptilz\Sql`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Sql.php) - MySQL query building
- [`\Ptilz\Str`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Str.php) - String
- [`\Ptilz\Sys`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Sys.php) - System/process/shell functions
- [`\Ptilz\V`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/V.php) - Variables/objects; `Var` is a reserved word :(

Instatiable classes:

- [`\Ptilz\CsvReader`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/CsvReader.php) - CSV reader
- [`\Ptilz\CsvWriter`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/CsvWriter.php) - CSV writer
- [`\Ptilz\File`](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/File.php) - Wrapper for functions that act on a file pointer

There's also [Exceptions](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Exceptions/) and [Comparables](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Comparables/) (for use with `usort`). More [Collection](https://bitbucket.org/mnbayazit/ptilz/src/tip/src/Comparables/) classes to come.

## License ##

Released under [the MIT license](http://opensource.org/licenses/mit-license.html).