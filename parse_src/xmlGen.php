<?php

function generateXML(){
    global $instruction_list;
    global $loc;

    $dom_doc = new DOMDocument('1.0','UTF-8');
    $dom_doc->formatOutput = true;

    $xml_doc = $dom_doc->createElement('program');
    $xml_doc->setAttribute('language', 'IPPcode23');
    $xml_doc = $dom_doc->appendChild($xml_doc);
    $lexema = array();
    $inst_counter = 0;
    $lexema = parse();
    if ($lexema[0] != NULL) {
        if ($lexema[0][0] != NULL) {
            if ($lexema[0][0] != TOKEN_HEADER) {
                exit(ERR_HEAD);
            }
        }
        else {
            exit(ERR_INTERNAL);
        }
    }
    else {
        exit(ERR_INTERNAL);
    }
    while($lexema[0][0] != TOKEN_EOF){
        $lexema = parse();
        switch ($lexema[0][0]) {
            case TOKEN_HEADER:
                exit(ERR_CODE);
            case TOKEN_EOF:
            case TOKEN_INSTRUCT:
                break;
            default:
                exit(ERR_CODE);
        }
        if (count($lexema) == 1 && $lexema[0][0] == TOKEN_EOF) {
            break;
        }
        elseif($lexema[0][0] == TOKEN_INSTRUCT){
            $loc++;
            $xmlInstruction = $dom_doc->createElement("instruction");
            $xmlInstruction->setAttribute("order", $loc);
            $xmlInstruction->setAttribute("opcode", $instruction_list[$lexema[0][1]]);
            switch ($lexema[0][1]){
                case 1:     # CREATEFRAME
                case 2:     # PUSHFRAME
                case 3:     # POPFRAME
                case 6:     # RETURN
                case 34:    # BREAK
                    if (count($lexema) != 1) {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 4:     # DEFVAR
                case 8:     # POPS
                    if (count($lexema) == 2 && $lexema[1][0] == TOKEN_VAR) {
                        $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                        $xmlArg1->setAttribute("type", "var");
                        $xmlInstruction->appendChild($xmlArg1);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 7:     # PUSHS
                case 22:    # WRITE
                case 32:    # EXIT
                case 33:    # DPRINT
                    if (count($lexema) == 2 && ($lexema[1][0] == TOKEN_VAR || $lexema[1][0] == TOKEN_CONST)) {
                        if ($lexema[1][0] == TOKEN_VAR) {
                            $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                            $xmlArg1->setAttribute("type", "var");
                        }
                        else {
                            $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][2]));
                            $xmlArg1->setAttribute("type", $lexema[1][1]);
                        }
                        $xmlInstruction->appendChild($xmlArg1);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 5:     # CALL
                case 28:    # LABEL
                case 29:    # JUMP
                    if (count($lexema) == 2 && ($lexema[1][0] == TOKEN_LABEL || $lexema[1][0] == TOKEN_TYPE)) {
                        $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                        $xmlArg1->setAttribute("type", "label");
                        $xmlInstruction->appendChild($xmlArg1);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 0:     # MOVE
                case 18:    # NOT
                case 19:    # INT2CHAR
                case 24:    # STRLEN
                case 27:    # TYPE
                    if (count($lexema) == 3 && $lexema[1][0] == TOKEN_VAR && ($lexema[2][0] == TOKEN_VAR ||
                            $lexema[2][0] == TOKEN_CONST)) {
                        $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                        $xmlArg1->setAttribute("type", "var");

                        if ($lexema[2][0] == TOKEN_VAR) {
                            $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][1]));
                            $xmlArg2->setAttribute("type", "var");
                        }
                        else {
                            $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][2]));
                            $xmlArg2->setAttribute("type", $lexema[2][1]);
                        }
                        $xmlInstruction->appendChild($xmlArg1);
                        $xmlInstruction->appendChild($xmlArg2);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 21:    # READ
                    if (count($lexema) == 3 && $lexema[1][0] == TOKEN_VAR && $lexema[2][0] == TOKEN_TYPE) {
                        $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                        $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][1]));
                        $xmlArg1->setAttribute("type", "var");
                        $xmlArg2->setAttribute("type", "type");
                        $xmlInstruction->appendChild($xmlArg1);
                        $xmlInstruction->appendChild($xmlArg2);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 9:     # ADD
                case 10:    # SUB
                case 11:    # MUL
                case 12:    # IDIV
                case 13:    # LT
                case 14:    # GT
                case 15:    # EQ
                case 16:    # AND
                case 17:    # OR
                case 20:    # STRI2INT
                case 23:    # CONCAT
                case 25:    # GETCHAR
                case 26:    # SETCHAR
                    if (count($lexema) == 4 && $lexema[1][0] == TOKEN_VAR && ($lexema[2][0] == TOKEN_VAR ||
                            $lexema[2][0] == TOKEN_CONST) && ($lexema[3][0] == TOKEN_VAR ||
                            $lexema[3][0] == TOKEN_CONST)) {
                        $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                        $xmlArg1->setAttribute("type", "var");

                        if ($lexema[2][0] == TOKEN_VAR) {
                            $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][1]));
                            $xmlArg2->setAttribute("type", "var");
                        }
                        else {
                            $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][2]));
                            $xmlArg2->setAttribute("type", $lexema[2][1]);
                        }
                        if ($lexema[3][0] == TOKEN_VAR) {
                            $xmlArg3 = $dom_doc->createElement("arg3", htmlspecialchars($lexema[3][1]));
                            $xmlArg3->setAttribute("type", "var");
                        }
                        else {
                            $xmlArg3 = $dom_doc->createElement("arg3", htmlspecialchars($lexema[3][2]));
                            $xmlArg3->setAttribute("type", $lexema[3][1]);
                        }
                        $xmlInstruction->appendChild($xmlArg1);
                        $xmlInstruction->appendChild($xmlArg2);
                        $xmlInstruction->appendChild($xmlArg3);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                case 30:    # JUMPIFEQ
                case 31:    # JUMPIFNEQ
                    if (count($lexema) == 4 && ($lexema[1][0] == TOKEN_LABEL || $lexema[1][0] == TOKEN_TYPE) &&
                        ($lexema[2][0] == TOKEN_VAR || $lexema[2][0] == TOKEN_CONST) && ($lexema[3][0] == TOKEN_VAR ||
                            $lexema[3][0] == TOKEN_CONST)) {
                        $xmlArg1 = $dom_doc->createElement("arg1", htmlspecialchars($lexema[1][1]));
                        $xmlArg1->setAttribute("type", "label");

                        if ($lexema[2][0] == TOKEN_VAR) {
                            $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][1]));
                            $xmlArg2->setAttribute("type", "var");
                        }
                        else {
                            $xmlArg2 = $dom_doc->createElement("arg2", htmlspecialchars($lexema[2][2]));
                            $xmlArg2->setAttribute("type", $lexema[2][1]);
                        }
                        if ($lexema[3][0] == TOKEN_VAR) {
                            $xmlArg3 = $dom_doc->createElement("arg3", htmlspecialchars($lexema[3][1]));
                            $xmlArg3->setAttribute("type", "var");
                        }
                        else {
                            $xmlArg3 = $dom_doc->createElement("arg3", htmlspecialchars($lexema[3][2]));
                            $xmlArg3->setAttribute("type", $lexema[3][1]);
                        }
                        $xmlInstruction->appendChild($xmlArg1);
                        $xmlInstruction->appendChild($xmlArg2);
                        $xmlInstruction->appendChild($xmlArg3);
                    }
                    else {
                        exit(ERR_LEX_SYN);
                    }
                    break;
                default:
                    exit(ERR_LEX_SYN);
            }

        }
        else{
            exit(ERR_LEX_SYN);
        }
        $xml_doc->appendChild($xmlInstruction);
    }
    echo $dom_doc->saveXML();
}