<?php
use \Digraph\Forms\Fields\Ajax\AbstractAutoComplete;

//This is a demo of how to create your own basic Ajax Autocomplete field
class NamesField extends AbstractAutoComplete
{

    /*
    This is the only required function for an autocomplete field
    In its simplest form it takes a query and returns a list of results
    Results are an array, with the key being the value that will be filled,
    and the value being the label that will be shown in the results list.
     */
    public function autocomplete($q)
    {
        $results = array();
        $fh = fopen(__DIR__.'/names.txt', 'r');
        while (($line = fgets($fh)) !== false) {
            $line = trim($line);
            if (($start = stripos($line, $q)) !== false) {
                $label = substr($line, 0, $start);
                $label .= '<strong>'.substr($line, $start, strlen($q)).'</strong>';
                $label .= substr($line, $start+strlen($q));
                $results[$line] = $label;
            }
        }
        fclose($fh);
        if (!$results) {
            //if you return a string it will be used as an error message
            return 'No results found';
        }
        //otherwise return the array of results
        return $results;
    }
}

//this one was kind of just for fun -- it autocompletes the prime factors of the input
//it also demostrates how to plug into the front end
class PrimeFactorsField extends AbstractAutoComplete
{
    /*
    Demonstrates how to plug into autocompletion at the front end. Field Tag will
    have an "autocomplete" event fired, and it will have the JSON from the server
    placed in its data-json attribute.
     */
    public function constructResources()
    {
        parent::constructResources();
        $this->registerInternalInitScript('$(".FieldTag-class-PrimeFactorsField").on("autocomplete",function(obj){ console.log( JSON.parse($(this).attr("data-json")) ); });');
    }

    /*
    return an array of results to populate the autocomplete UI, keyed by the
    value that will be placed in the text field.
    'label' is all that is required, and it is the HTML that will represent
    the entry in the UI
    Add any other data you like, if you have use of it on the front end somehow.
     */
    public function autocomplete($n)
    {
        $factors = $this->pfactor($n);
        $out = array();
        foreach ($factors as $factor => $count) {
            $out[$factor] = array(
                'label' => $count.' multiples of '.$factor.' in '.$n,
                'factor' => $factor,
                'count' => $count
            );
        }
        return $out;
    }

    private function pfactor($n)
    {
    // max_n = 2^31-1 = 2147483647
        $d=2;
        $factors = array();
        $dmax = floor(sqrt($n));
        $sieve = array();
        $sieve = array_fill(1, $dmax, 1);
        do {
            $r = false;
            while ($n%$d==0) {
                @$factors[$d]++;
                $n/=$d;
                $r = true;
            }
            if ($r) {
                $dmax = floor(sqrt($n));
            }
            if ($n>1) {
                for ($i=$d; $i<=$dmax; $i+=$d) {
                    $sieve[$i]=0;
                }
                do {
                    $d++;
                } while (@$sieve[$d]!=1 && $d<$dmax);
                if ($d>$dmax) {
                    @$factors[$n]++;
                }
            }
        } while ($n>1 && $d<=$dmax);
        return $factors;
    }
}
