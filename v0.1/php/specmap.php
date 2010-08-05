<?php
    // text escape map and math escape map should contain the same keys

    $textescmap = array(
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
        '>' => '\textgreater{}'
        // not special but typography
        //u'\u00a9' => '\textcopyright{}',
        //u'\u2011' => '\mbox{-}'
    );

    $mathescmap = array(
        '\\' => '\backslash{}',
        '{' => '\{',
        '}' => '\}',
        '$' => '\$',
        '&' => '\&',
        '#' => '\#',
        '^' => '\^{}',
        '_' => '\_',
        '~' => '\~{}',
        '%' => '\%',
        '|' => '|',
        '<' => '<',
        '>' => '>'
        // not special but typography
        //u'\u00a9' => '\copyright{}',
        //u'\u2011' => '-'
    );

    //
    // Although these symbols are not special, it is better to escape them
    // because in as-is form they are not so good
    //
    //typographymap = {
    //  u'\u00a0' => '~'
    //}

    //textescmap.update(typographymap)
    //mathescmap.update(typographymap)

    //
    // Mapping from spec/@cat to symbols
    //
    $tocharmap = array(
        'esc'     => '\\',
        'bg'      => '{',
        'eg'      => '}',
        'mshift'  => '$',
        'align'   => '&',
        'parm'    => '#',
        'sup'     => '^',
        'sub'     => '_',
        'tilde'   => '~',
        'comment' => '%',
        'vert'    => '|',
        'lt'      => '<',
        'gt'      => '>',
        'nl'      => '\n',
        'space'   => ' ',
        'nil'     => ''
    );

?>