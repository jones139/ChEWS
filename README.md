# ChEWS - Chemical Element Word Solver
Converts a word given on the command line to chemical element symbols,
if this is possible.

Returns the first solution found that can spell the word with chemical symbols.
The solution is a list of element atomic numbers.

# Usage
Usage: ChEWS.py [options] <argument> ...

ChEWS - Chemical Element Word Solver - spell names using chemical element
symbols.

Options:
  --version   show program's version number and exit
  -h, --help  show this help message and exit

# Examples
./ChEWS.py laura
ChEWS - Chemical Element Word Solver
found solution - target = laura answer = LaURa
[57, 92, 88]

./ChEWS.py nicola
ChEWS - Chemical Element Word Solver
found solution - target = nicola answer = NICOLa
[7, 53, 6, 8, 57]

./ChEWS.py gina
ChEWS - Chemical Element Word Solver
   Oh no - failed completely
Failed to Find Solution for gina


# Credits
Elements Data from 
https://raw.githubusercontent.com/diniska/chemistry/master/PeriodicalTable/periodicTable.json