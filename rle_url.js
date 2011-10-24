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

function RLE_URL( options ) {
	this.key = {"0":"n","1":"j","11":"B","00":"b","1n":"A","0n":"a"};
	this.repeat_limit = 500;
	
	this.runlength = function( c_input, t_input, c_index ) {
		var t = "";
		t += t_input + "$";	// inflate string by discardable marker
		var i = 0;
		
		for( i = c_index + 1; i < t.length; i++ ) {
			if( c_input != t[i] ) return( i - c_index );
		}
		return( -1 );
	}
	
	this.encode = function( t ) {
		var n = "";
		
		for( i = 0; i < t.length; i++ ) {
			var c = t[i];
			var f = t[i+1];
			var l = this.runlength(c,t,i);
			
			if( l == 1 ) n += (c == '1') ? this.key["1"] : this.key["0"];
			if( l == 2 ) {
				n += (c == '1') ? this.key["11"] : this.key["00"];
				i += 1;
			}
			if( l > 2 ) {
				 n += (c == 1 ? this.key["1n"]: this.key["0n"]) + l;
				 i += (l - 1);
			}
		}
		return( new String(n) );	
	}
	
	this.decode_snippet = function( s ) {
		if( s == this.key["1"] || s == this.key["0"] )
			return( (s == this.key["1"]) ? '1' : '0' );
		if( s == this.key["11"] || s == this.key["00"] )
			return( (s == this.key["11"]) ? '11' : '00' );

		var prefix = s.substr(0,1);
		var suffix = parseInt( s.substr(1) );
		
		if( suffix > this.repeat_limit ) return( false );
		return( new Array(parseInt(suffix)+1).join(
			(prefix == this.key["1n"]) ? '1' : '0')
		);
	}
	
	this.decode = function( rle_string ) {

		var pattern = 
			this.key["0n"] + "([0-9]+)|" +
			this.key["1n"] + "([0-9]+)|" +
			this.key["1"] + "|" + 
			this.key["0"] + "|" + 
			this.key["11"] + "|" + 
			this.key["00"];

		var out = "";
		a = rle_string.match(new RegExp(pattern, "g" ) );
		for( i = 0; i < a.length; i++ ) {
			out += this.decode_snippet( a[i] );
		}
		return( new String(out) );
	}
}
