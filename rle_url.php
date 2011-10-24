<?php
/*
================================================================================
RLE URL Encoder/Decoder
================================================================================
Author: 	Dino Beslagic (pp19dd at gmail.com)
Homepage: 	http://pp19dd.com/rle-url/
About:		RLE URL encoder is a tiny PHP and JavaScript library that helps
			encode or decode long query strings (ex: country checkboxes) into
			short predictable string with a simple run-length encoding algorithm

This library is intended to help shrink long URLs from something like:
?country[]=Algeria&country[]=Angola&country[]=Barbados... (and 100s more)
into a predictable, short-encoded version such as: ?c=a81ja104ja11

notes:
	*	User needs to encode each checkbox as a 1 or 0, ex: 111010101110101
		And maintain forward-reverse relationship as they like
	*	The library will also decode ?c=a81ja104ja11 into a reversible set of values
	*	There is a compatible PHP version of this library

For usage examples in PHP and JavaScript, see the README file

================================================================================
LICENSE
================================================================================
No license, so anything goes.  However, short attribution would be polite.

Use this library at own risk.
================================================================================

*/

class RLE_URL {
	public $key = array(
		"0" => "n",		"1" => "j",
		"11" => "B",	"00" => "b",
		"1n" => "A",	"0n" => "a"
	);

	public $repeat_limit = 500;
	
	function setKeys( $new_key = array() ) {
		
		// array merge handles duplicate keys poorly
		foreach( $new_key as $k => $v ) {
			$this->key[$k] = $v;
		}
	}

	function runlength( $c_input, $t_input, $c_index ) {
		$t = $t_input . "X";	// inflate string by discardable marker
		$i = 0;
		
		for( $i = $c_index + 1; $i < strlen($t); $i++ ) {
			if( $c_input != substr($t,$i,1) ) return( $i - $c_index );
		}
		return( -1 );
	}
	
	function encode( $t ) {
		$n = "";
		
		for( $i = 0; $i < strlen($t); $i++ ) {
			$c = substr($t, $i, 1 );
			$f = substr($t, $i + 1, 1 );
			$l = $this->runlength( $c, $t, $i );
			
			if( $l == 1 ) $n .= ($c == '1') ? $this->key["1"] : $this->key["0"];
			if( $l == 2 ) {
				$n .= ($c == '1') ? $this->key["11"] : $this->key["00"];
				$i += 1;
			}
			if( $l > 2 ) {
				 $n .= ($c == 1 ? $this->key["1n"] : $this->key["0n"]) . $l;
				 $i += ($l - 1);
			}
		}
		return( $n );
	}	
	
	// decodes a fragment such as j to 1, n to 0
	function decode_snippet( $s ) {
		if( $s === $this->key["1"] || $s === $this->key["0"] )
			return( ($s === $this->key["1"]) ? '1' : '0' );

		if( $s === $this->key["11"] || $s === $this->key["00"] )
			return( ($s === $this->key["11"]) ? '11' : '00' );
		
		$prefix = substr($s, 0, 1);
		$suffix = intval(substr($s, 1));
		
		// limit exceeded - possible attack / corrupt url?
		if( $suffix > $this->repeat_limit ) return( false );
		return( str_repeat(($prefix === $this->key["1n"]) ? '1':'0', $suffix) );
	}
	
	// decodes a snippet such as a6ja77j to 1010101001
	function decode( $rle_string ) {
		$pattern = sprintf(
			"/%s([0-9]+)|%s([0-9]+)|%s|%s|%s|%s/",
			preg_quote( $this->key["0n"] ),
			preg_quote( $this->key["1n"] ),
			preg_quote( $this->key["1"] ),
			preg_quote( $this->key["0"] ),
			preg_quote( $this->key["11"] ),
			preg_quote( $this->key["00"] )
		);
		
		$a = preg_match_all( $pattern, $rle_string, $r );
		$out = '';

		foreach( $r[0] as $k => $v ) {
			$out .= $this->decode_snippet( $v );
		}
		return( $out );
	}
}

?>