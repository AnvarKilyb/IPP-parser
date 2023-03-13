<?php
include 'parse_src/scanner.php' ;
include 'parse_src/xmlGen.php';
const ERR_OK = 0;
const ERR_ARG = 10;
const ERR_READ = 11;
const ERR_WRITE = 12;
const ERR_INTERNAL = 99;
const ERR_HEAD = 21;
const ERR_CODE = 22;
const ERR_LEX_SYN = 23;

//tokens
const TOKEN_EOF = 100;
const TOKEN_HEADER = 101;
const TOKEN_CONST = 102;
const TOKEN_VAR = 103;
const TOKEN_INSTRUCT = 104;
const TOKEN_TYPE = 105;
const TOKEN_LABEL = 106;

$instruction_list = array(
    // Frames
    "MOVE", // 0
    "CREATEFRAME",
    "PUSHFRAME",
    "POPFRAME",
    "DEFVAR",
    "CALL",
    "RETURN", // 6
    // Data array
    "PUSHS",
    "POPS",
    // Arithmetic, relations, bool, convertation instructions
    "ADD", // 9
    "SUB",
    "MUL",
    "IDIV",
    "LT",
    "GT",
    "EQ",
    "AND",
    "OR",
    "NOT",
    "INT2CHAR",
    "STRI2INT", // 20
    // IN and OUT instructions
    "READ",
    "WRITE",
    // Work with array
    "CONCAT", // 23
    "STRLEN",
    "GETCHAR",
    "SETCHAR", // 26
    // Work with types
    "TYPE",
    // Processes
    "LABEL", // 28
    "JUMP",
    "JUMPIFEQ",
    "JUMPIFNEQ",
    "EXIT",
    // Debug instructions
    "DPRINT",
    "BREAK" // 34
);

// MAIN
checkArgument($argv, $argc);
$stdin = STDIN;
//$stdin = fopen("tests/header/code200.src", "r");
$stdout = STDOUT;
$stderr = STDERR;

$loc = 0;
$comments = 0;
generateXML();
exit(ERR_OK);


//echo isInstruction("DEFVAR\n") . "\n";
// FUNCTIONS
function isInstruction($word) {
    global $instruction_list;

    foreach ($instruction_list as $inst) {
        if (preg_match("~^" . $inst . "$~i", $word)) {
            return array_search($inst, $instruction_list);
        }
    }

    return -1;
}

function checkArgument($argv, $num){
    for($i = 1; $i < $num; $i++){
        if($argv[$i] == "--help"){
            if($num > 2){
                echo "\t--Cannot pass more arguments with '--help'\n";
                exit(ERR_ARG);
            }
            echo "\t--Script is written with PHP language 8.1\n";
            echo "\t--Reads from stdin source code IPPcode23 and Writes to stdout XML code\n";
            echo "\t--Script controls syntax and lexical code correctness\n";
            exit (ERR_OK);
        }
    }
}