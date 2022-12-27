<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ApiFilter {

    // protected $safeParms = [];

    // protected $columnMap = [];

    // protected $operatorMap = [];

    public function transform(Request $request) {

        $eloQuery = [];

        foreach($this->safeParms as $parm => $operators) {

            // \Log::info($parm);
            // \Log::info($operators);
            $query = $request->query($parm);            
            if (!isset($query)) {
                continue;
            }
            $column = $this->columnMap[$parm] ?? $parm;            

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }                
            }
        }               
        // \Log::info($eloQuery); 
        return $eloQuery;
    }
}