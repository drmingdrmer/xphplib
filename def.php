<?
function xpl_isdef( $name ) {
    return defined( $name );
}

function xpl_getdef( $name, $default=NULL ) {
    if ( xpl_isdef( $name ) ) {
        return constant( $name );
    }
    else {
        return $default;
    }
}

function xpl_getconf( $subname, $default=NULL ) {
    $pref = 'XPL_CONF_';
    $n = $pref . strtoupper($subname);
    return xpl_getdef( $n, $default );
}

function xpl_or( $a, $b ) {
    if ( $a === NULL ) {
        return $b;
    }
    else {
        return $a;
    }
}
?>
