<?php
    // class-wrapper for escape table
    class textescmap {
        var $tem;
        // text escape map and math escape map should contain the same keys
        function textescmap() {
            $this->tem = array(
                '\\' => '\textbackslash{}',
                '{' => '\{',
                '}' => '\}',
                '$' => '\textdollar{}',
                '&' => '\&',
                '#' => '\#',
                '^' => '\^{}',
                '_' => '\_',
                '~' => '\textasciitilde{}',
                '%' => '\%',
                '|' => '\textbar{}',
                '<' => '\textless{}',
                '>' => '\textgreater{}',
                // not special but typography
                "\x00a9" => '\textcopyright{}',
                "\x2011" => '\mbox{-}'
            );
            //
            // Although these symbols are not special, it is better to escape them
            // because in as-is form they are not so good
            //
            $typographymap = array(
                "\x00a0" => '~'
            );
            // update array
            $this->tem = array_merge($this->tem, $typographymap); 

        }

        function get() {
            return $this->tem;
        }
    }


    //textescmap.update(typographymap)

?>