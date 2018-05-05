<?PHP 
session_start();
$input = file_get_contents($_SESSION["file"]);
$raw_input = explode("\n", $input);
$tosolve = array();
foreach ($raw_input as $select){
    $eIndex = strpos($select, '#');
    $conun = substr($select, 0, $eIndex);
    if (empty($conun) === FALSE)
        array_push($tosolve, $conun);
}

$ref = array(   "A" => FALSE, "B" => FALSE, "C" => FALSE, "D" => FALSE,
                "E" => FALSE, "F" => FALSE, "G" => FALSE, "H" => FALSE,
                "I" => FALSE, "J" => FALSE, "K" => FALSE, "L" => FALSE,
                "M" => FALSE, "N" => FALSE, "O" => FALSE, "P" => FALSE,
                "Q" => FALSE, "R" => FALSE, "S" => FALSE, "T" => FALSE,
                "U" => FALSE, "V" => FALSE, "W" => FALSE, "X" => FALSE,
                "Y" => FALSE, "Z" => FALSE
            );

$loop = FALSE;
//============================================================================";

function query($tosolve){
    foreach ($tosolve as $line){
        $qLine = strpos($line, "?");
        if ($qLine === 0){
            $qry = str_split(trim(substr($line, 1)));
            return ($qry);
        }
    }
}

$query = query($tosolve);

function initFacts($tosolve){
    foreach ($tosolve as $fact){
        $isHere = strpos($fact, "=");
        if ($isHere === 0){
            $iFact = trim(substr($fact, 1));
            $iArr = str_split($iFact);
            return ($iArr);
        }
    }
}

function setState($inFacts, $ref){
    foreach ($inFacts as $fact){
        $ref[$fact] = TRUE;
    }
    return ($ref);
}

if ($_SESSION["altFacts"])
	$inFacts = str_split($_SESSION["altFacts"]);
else
	$inFacts = initFacts($tosolve);
$ref = setState($inFacts, $ref);

function sieve($tosolve){
    $testCont = array();
    foreach ($tosolve as $test){
        $isTest = strpos($test, "=");
        if ($isTest > 0){
            array_push($testCont, $test);
        }
    }
    return ($testCont);
}

$isolData = sieve($tosolve);

function typeSign($char){
	$str = "+|^()";
	if (!strpos($str, $char))
		return (FALSE);
	else 
		return (TRUE);
}

function inputValidation() {

	global $isolData;
	global $inFacts;
	global $query;

	foreach ($query as $ask){
		if (ctype_alpha($ask) === FALSE){
			$_SESSION["invalid"] = "Invalid query!";
			break ;
		}
	}
	foreach ($inFacts as $fact){
		if (ctype_alpha($fact) === FALSE){
			$_SESSION["invalid"] = "Invalid Facts!";
			break ;
		}
	}
	print_r($workData);
	
	foreach ($isolData as $element){
		$workData = explode(" ", $element);
		foreach ($workData as $work){
			//echo $work;
			$message = "Invalid rule character";
			if (strlen($work) === 1){
				if (ctype_alpha($work) === FALSE && typeSign($work) === FALSE)
					$_SESSION["invalid"] = $message;
				break ;
			}
			elseif (strlen($work) === 2){
				if (!ereg("^![A-Z]$", $work) || $work !== "=>")
					$_SESSION["invalid"] = $message;	
				break ;
			}
			elseif ($work !== "<=>"){
				$_SESSION["invalid"] = $message;
				break ;
			}		
		}
	}
}


//============================================================================";
								//copying the rulset into a session variable.;
$_SESSION[$ruleset] = $isolData;
//============================================================================";

function leftSide($line){
    $end = strpos($line, "<");
    if ($end === FALSE)
        $end = strpos($line, "=");
    $left = trim(substr($line, 0, $end));
    return ($left);
}

function rightSide($line){
    $start = strpos($line, ">") + 1;
    $right = trim(substr($line, $start));
    return ($right); 
}

function impState($line){
    $state = strpos($line, "<");
    if ($state != FALSE)
        return (1);
    else 
        return (0);
}

function lRules($leftS){
    global $ref;
    $value = precede($leftS);
    $tStore = TRUE;
    $lRes = array();
    $iterOn = 0;
    $statement;
    foreach($leftS as $item){
       if (ctype_alpha($item) === TRUE){
           if (strlen($item) === 1){
                if ($ref[$item] === TRUE)
                    $statement = $item." is TRUE ";
                else
                    $statement = $item." is FALSE ";
           }
       }
       elseif (strlen($item) === 2){
            $str = str_split($item);
            if ($ref[$str[1]] === TRUE)
                $statement = $item." is FALSE ";
            else
                $statement = $item." is TRUE ";
        }else{
            if ($item === "+"){
                $statement = "  ";
            }
            elseif ($item === "|"){
                $statement = "  ";
            }
            elseif ($item === "^"){
                $statement = "  ";
            }
        }
        array_push($lRes, $statement);
    }
    $result = implode($lRes);
    $ret = array($value, $result);
    return ($ret);
}

$time = 0;

function rRules($rightS, $lState){
    global $ref;
    global $loop;
    global $time;
    $rRes = array();
    foreach ($rightS as $item){
		if(in_array("|", $rightS)){
			$value = " is undetermined ";
		}
		elseif (ctype_alpha($item) === TRUE){
            if ($lState === TRUE){
                $ref[$item] = TRUE;
                $value = " is TRUE ";
                if ($time < 50){
                    $time = $time + 1;
                    $loop = TRUE;
                }
			}
            else{
                $value = " is FALSE ";
            }
        }
        elseif (strlen($item) === 2){
            $str = str_split($item);
            $index = $str[1];
            if ($lState === FALSE && $ref[$index] === FALSE){
                $value = " is TRUE ";
			}
            elseif ($lState === TRUE && $ref[$index] === TRUE){
                $value = " is FALSE ";
            }
            elseif ($lState === FALSE && $ref[$index] === TRUE){
                $value = " is FALSE ";
            }
            elseif ($lState === TRUE && $ref[$index] === FALSE){
                $value = " is TRUE ";
            }
        }
        if (ctype_alpha($item) === TRUE || strlen($item) === 2){
            $statement = $item.$value;
            array_push($rRes, $statement);
        }
    }
    $rRet = implode($rRes);
    return  ($rRet);
}

function pValue($symbol){
    if ($symbol === "(")
        return (4);
    elseif ($symbol === ")")
        return (5);
    elseif ($symbol === "+")
        return (3);
    elseif ($symbol === "|")
        return (2);
    elseif ($symbol === "^")
        return (1);
}

function evalState($statement){
    $evalStack = array();
    global $ref;
    $keys = array_keys($statement,"");
    foreach ($keys as $k)
        unset($statement[$k]);
    if (count($statement) === 1){
            $value = $ref[$statement[0]];
            return ($value);
    }
    foreach ($statement as $item){
        if (count($statement) === 1){
            if ($ref[$item] === TRUE)
                $value = TRUE;
            else
                $value = FALSE;
            break ;
        }
        else{
            if (ctype_alpha($item) === TRUE || strlen($item) === 2)
                array_push($evalStack, $item);
            else{
                $val1 = array_pop($evalStack);
                $val2 = array_pop($evalStack);
                if ($val1 === 5)
                    $val1 = TRUE;
                elseif ($val1 === 9)
                    $val1 = FALSE;
                else{
                    if (strlen($val1) == 2){
                        $val1 = str_split($val1);
                        $val1 = $val1[1];
                        if ($ref[$val1] === TRUE)
                            $val1 = FALSE;
                        else
                            $val1 = TRUE;
                    }
                    else
                        $val1 = $ref[$val1];
                }

                if ($val2 === 5)
                    $val2 = TRUE;
                elseif ($val2 === 9)
                    $val2 = FALSE;
                else{
                    if (strlen($val2) == 2){
                        $val2 = str_split($val2);
                        $val2 = $val2[1];
                        if ($ref[$val2] === TRUE)
                            $val2 = FALSE;
                        else
                            $val2 = TRUE;
                    }
                    else
                        $val2 = $ref[$val2];
                }
                    
                if ($item === "+"){
                    if ($val1 === TRUE && $val2 === TRUE)
                        $value = 5;
                    else
                        $value = 9;
                }
                elseif ($item === "|"){
                    if ($val1 === TRUE || $val2 === TRUE)
                        $value = 5;
                    else
                        $value = 9;
                }
                elseif ($item === "^"){
                    if (($val1 === TRUE && $val2 === FALSE) || ($val2 === TRUE && $val1 === FALSE))
                        $value = 5;
                    else
                        $value = 9;
                }
                array_push($evalStack, $value);
            }
        } 
    }
    if ($value === 5)
        $value = TRUE;
    else
        $value = FALSE;
    return ($value);
}

function precede($rules){
    $operatorStack = array();
    $operandStack = array();
    foreach ($rules as $item){
        if (ctype_alpha($item) === TRUE || strlen($item) === 2){
            array_push($operandStack, $item);
        }
        else{
            if (pValue($item) === 4){
                array_push($operatorStack, $item);
            }
            elseif (pValue($item) === 5){
                foreach (array_reverse($operatorStack) as $ops)
                {
                    if ($ops != "(")
                        array_push($operandStack, array_pop($operatorStack));
                    else{
                        array_pop($operatorStack);
                        break ;
                    }
                }
            }
            elseif (pValue($item) > 0 && pValue($item) < 4){
                $emp = count($operatorStack);
                if ($emp === 0)
                    array_push($operatorStack, $item);
                elseif (pValue(array_reverse($operatorStack)[0]) === 4)
                    array_push($operatorStack, $item);
                elseif (pValue($item) < pValue(array_reverse($operatorStack)[0])){
                    array_push($operandStack, array_pop($operatorStack));
                    array_push($operatorStack, $item);
                }
                elseif (pValue($item) === pValue(array_reverse($operatorStack)[0]))
                    array_push($operandStack, $item);
                else
                    array_push($operandStack, $item);
            }
        }
    }
    foreach ($operatorStack as $operator)
        array_push($operandStack, array_pop($operatorStack));
    return (evalState($operandStack));
}

function theExpert($lData){
    $lRes = lRules(explode(" " ,$lData["left"]));
    $rRes = rRules(explode(" ",$lData["right"]), $lRes[0]);
    $statement = $lRes[1]." SO ".$rRes;
    return ($statement);
}

$results = array();
$contras = array(); 

function rules(){
    global $contras;
    global $isolData;
    foreach ($isolData as $data){
        $leftS = leftSide($data);
        $rightS = rightSide($data);
        $impState = impState($data);
        $sides[] = array($leftS, $impState, $rightS);
    }
    foreach ($sides as $side){
        if ($side[1] === 1){
            $temp1 = $side[0];
            $temp2 = $side[2];  
            foreach ($sides as $fact){
                if (strcmp($temp1, $fact[0]) === 0){
                    if (strcmp($fact[2], $temp2) != 0){
                        $cmp1 = explode(" ", $fact[2]);
                        $cmp2 = explode(" ", $temp2);
                        $i = 0;
                        foreach ($cmp1 as $element){
                            if ($element != $cmp2[$i]){
                                if (strpos($element, $cmp2[$i]))
                                    $contras[] = ($side[0]." => ".$side[2]." contradicts ".$fact[0]." => ".$fact[2]);
                                elseif (strpos($cmp2[$i], $element))
                                    $contras[] = ($side[0]." => ".$side[2]." contradicts ".$fact[0]." => ".$fact[2]);
                            }
                        }
                    } 
                }
            }
            foreach ($sides as $fact){
                if (strcmp($temp1, $fact[2]) === 0){
                    if (strcmp($fact[0], $temp2) != 0){
                        $cmp1 = explode(" ", $fact[0]);
                        $cmp2 = explode(" ", $temp2);
                        $i = 0;
                        foreach ($cmp1 as $element){
                            if ($element != $cmp2[$i]){
                                if (strpos($element, $cmp2[$i]))
                                    $contras[] = ($side[0]." => ".$side[2]." contradicts ".$fact[0]." => ".$fact[2]);
                                elseif (strpos($cmp2[$i], $element))
                                    $contras[] = ($temp1." => ".$temp2." contradicts ".$fact[0]." => ".$fact[2]);
                            }
                        }
                    } 
                }
            }
        }
    }
}

function rulesTwo(){
    global $contras;
    global $isolData;
    foreach ($isolData as $data){
        $leftS = leftSide($data);
        $rightS = rightSide($data);
        $impState = impState($data);
        $sides[] = array($leftS, $impState, $rightS);
    }
    foreach ($sides as $side){
            $temp1 = $side[0];
            $temp2 = $side[2];  
            foreach ($sides as $fact){
                if (strcmp($temp1, $fact[0]) === 0){
                    if (strcmp($fact[2], $temp2) != 0){
                        $cmp1 = explode(" ", $fact[2]);
                        $cmp2 = explode(" ", $temp2);
                        $i = 0;
                        foreach ($cmp1 as $element){
                            if ($element != $cmp2[$i]){
                                if (strpos($element, $cmp2[$i]))
                                    $contras[] = ($side[0]." => ".$side[2]." contradicts ".$fact[0]." => ".$fact[2]);
                                elseif (strpos($cmp2[$i], $element))
                                    $contras[] = ($side[0]." => ".$side[2]." contradicts ".$fact[0]." => ".$fact[2]);
                            }
                        }
                    } 
                }
            }
    }
}

function expertSys($isolData){
    global $loop;
    global $results;
    foreach($isolData as $line){
        $leftS = leftSide($line);
        $rightS = rightSide($line);
        $impState = impState($line);
        $lineData = array(
            "left" => $leftS,
            "state" => $impState,
            "right" => $rightS
        );
        $res = theExpert($lineData);
        array_push($results, $res);
    }
    if ($loop === TRUE){
        $loop = FALSE;
        expertSys($isolData);
    }
}

function theEnd(){
    global $results;
    global $isolData;
    $num = count($isolData);
    expertSys($isolData);
    $i = 0;
    $temp = array_reverse($results);
    while ($i < $num){
        $final[] = $temp[$i];
        $i++;
    }

    $final = array_reverse($final);
    foreach ($final as $rule)
        echo $rule."<br />";
}

function qry()
{
    global $ref;
    global $query;
    foreach ($query as $q){
        if ($ref[$q] === TRUE)
			echo $q." is true ";
		elseif ($ref[$q] === 2)
			echo $q." is undetermined ";
        else
            echo $q." is false ";
    }
}

inputValidation();
rules();
rulesTwo();
$dictions = Array();
if ($contras){
	foreach ($contras as $contra) {	
		if (in_array($contra, $dictions) === FALSE)
			$dictions [] = $contra;
	}
	$_SESSION["dictions"] = $dictions;
}
?>
