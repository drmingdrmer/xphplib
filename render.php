<?
require_once( 'def.php' );

/*
 * $p = $_SERVER[ 'SCRIPT_FILENAME' ];
 * $pi = pathinfo( $p );
 * 
 * 
 * var_dump( $pi );
 * 
 * // var_dump( $_SERVER );
 */

class XPL_Render {

    function __construct( $root=NULL, $ctx=NULL ) {
        $this->root = xpl_or( $root,
            xpl_getconf( 'RENDER_TEMPLATE_ROOT' ) );
        $this->ctx = $ctx;
    }

    function render( $tmplname, $data, $ctx=NULL ) {
        include( $this->root . "/$tmplname.php" );
    }
}

function xpl_render( $tmplname, $data, $ctx=NULL ) {
    $rnd = new XPL_Render( NULL, $ctx );
    $rnd->render( $tmplname, $data, $ctx );
}

?>
