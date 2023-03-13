<?php


function parse(){
    global $stdin;
    //global $comments;
    $result = array();

    while(true){
        $sentence = fgets($stdin);
        // EOF
        if($sentence == false){
            array_push($result, array(TOKEN_EOF));
            return $result;
        }
        // Comment or new line
        if (preg_match('~^\s*#~', $sentence) || preg_match('~^\s*$~', $sentence)) {
            continue;
        }
        # Check if sentence does not have comment at the end
        $split_comment = explode("#", $sentence);
        $split_word = preg_split("~\s+~", $split_comment[0]);
        if (end($split_word) == "")
            array_pop($split_word);
        if ($split_word[0] == "")
            array_shift($split_word);

        break;
    }
    $instr_number = -1;
    $isStart = true;
    foreach ($split_word as $word){
        if(preg_match("~@~", $word)){
            if(preg_match("~^(int|bool|string|nil)~", $word)){
                if (
                    preg_match('~^int@[+-]?[0-9]+$~', $word) ||
                    preg_match('~^nil@nil$~', $word) ||
                    preg_match('~^bool@(true|false)$~', $word) ||
                    preg_match('~^string@$~', $word) ||
                    (preg_match('~^string@~', $word) &&
                        !preg_match('~(\\\\($|\p{S}|\p{P}\p{Z}|\p{M}|\p{L}|\p{C})|(\\\\[0-9]{0,2}($|\p{S}|\p{P}\p{Z}|\p{M}|\p{L}|\p{C}))| |#)~', $word))
                ) {
                    $token = array_merge(array(TOKEN_CONST), explode("@", $word, 2));
                    array_push($result, $token);
                }else{
                    exit(ERR_LEX_SYN);
                }

            }else{
                if (preg_match('/^(GF|LF|TF)@(_|-|\$|&|%|\*|!|\?|[a-zA-Z])(_|-|\$|&|%|\*|!|\?|[a-zA-Z0-9])*$/', $word)) {
                    array_push($result, array(TOKEN_VAR, $word));
                }
                else {
                    exit(ERR_LEX_SYN);
                }
            }
        }
        // Label, labeltype, header, instruction
        else{
            if (preg_match('~^(int|bool|string|nil)$~', $word)) {
                //labelType
                array_push($result, array(TOKEN_TYPE, $word));
            }
            else{
                if (preg_match('~^\.ippcode23$~i', $word)) {
                    //Header
                    array_push($result, array(TOKEN_HEADER));
                }
                else{
                    $instr_number = isInstruction($word);
                    if ($instr_number != -1 && $isStart) {
                        //instruction
                        array_push($result, array(TOKEN_INSTRUCT, $instr_number));
                    }
                    else {
                        //label
                        if (preg_match('~^[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*$~', $word)) {
                            array_push($result, array(TOKEN_LABEL, $word));
                        }
                        else {
                            //if first sentence is wrong, return header error
                            if($isStart)
                                exit(ERR_HEAD);
                            //error
                            exit(ERR_LEX_SYN);
                        }
                    }
                }
            }
        }
        $isStart = false;
    }
    return $result;
}