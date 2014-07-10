<?php

class My {

    function logdie( $mes ) {
        $fn = '/var/log/my.log';
        error_log( $mes, 3, $fn );
        exit();
    }

    function __construct( $a ) {
        $this->my = mysql_connect( $a['host'], $a['username'], $a['password'] );
        if ( $this->my == False ) {
            $mes = mysql_errno();
            My::logdie( $mes );
        }

        $rc = mysql_select_db( $this->my, $a['dbname'] );
        if ( $rc === False ) {
            My::logdie( "failure select db ".$a['dbname'] );
        }
    }

    function __destruct() {
        mysql_close( $this->my );
    }

    function query( $sql ) {

        $r = mysql_query( $sql, $this->my );
        if ( $r === False ) {
            My::logdie( mysql_errno() . " $sql" );
        }

        $rows = array();
        while ($row = mysql_fetch_assoc($r)) {
            $rows[] = $row;
        }
        mysql_free_result( $r );

        return $rows;
    }

    function run_sql( $sql ) {

        $r = mysql_query( $sql, $this->my );
        if ( $r === False ) {
            My::logdie( mysql_errno() . " $sql" );
        }

        return array(
            errno=> mysql_errno( $this->my ),
            error=> mysql_error( $this->my ),
            insert_id=> mysql_insert_id( $this->my ),
            affected_rows=> mysql_affected_rows( $this->my ) );
    }

    function esc( $str ) {
        return '"' . mysql_real_escape_string( $str, $this->my ) . '"';
    }

    function _fields( $fields ) {
        $rst = array();
        foreach ($fields as $field) {
            array_push( $rst, "`$field`" );
        }
        return implode( ",", $rst );
    }

    function _values( $row, $keys ) {
        $rst = array();
        foreach ($keys as $k) {
            array_push( $rst, $this->esc( $row[$k] ));
        }
        return implode( ",", $rst );
    }

    function _assignments( $row ) {
        $rst = array();
        foreach ($row as $k=>$v) {
            array_push( $rst, "`$k`=" . $this->esc( $v ) );
        }
        return implode( ",", $rst );
    }

    function _where( $conditions ) {
        $rst = array();
        foreach ($conditions as $k=>$v) {
            array_push( $rst, "`$k`=" . $this->esc( $v ) );
        }
        return implode( ",", $rst );
    }

    function add( $row ) {
        $keys = array_keys( $row );
        $sql = "INSERT IGNORE INTO " . $this->table
            . " (" . $this->_fields( $keys ) . ")"
            . " VALUES (" . $this->_values( $row, $keys ) . ")";
        return $this->run_sql( $sql );
    }

    function set( $row ) {
        $keys = array_keys( $row );
        $sql = "INSERT INTO " . $this->table
            . " (" . $this->_fields( $keys ) . ")"
            . " VALUES (" . $this->_values( $row, $keys ) . ")"
            . " ON DUPLICATE KEY UPDATE " . $this->_assignments( $row )
            ;
        return $this->run_sql( $sql );
    }

    function get( $idents ) {
        $sql = "SELECT " . $this->_fields( $this->fields )
            . " FROM " . $this->table
            . " WHERE " . $this->_where( $idents )
            . " LIMIT 1";

        $rst = $this->query( $sql );
        return $rst[ 0 ];
    }
}

class KV extends My {
    protected $table = "test";
    protected $fields = array( "key", "val" );
}
