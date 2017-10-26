<?php

class NetworkNoticeHooks {


	
	public static function onParserFirstCallInit( $parser ) {

	}


	private static function echoNotice( $row ) {

		echo '<div style="background-color:' . $row->{'bgcolor'}  .  '; margin-top:10px; border-color:' . $row->{'bordercolor'}  .  '; display:block; text-align:center; padding:5px; margin-bottom:20px; color:#444444; border-left:5px solid ' . $row->{'bordercolor'}  .  ';">' . $row->{'notice_text'}  . '</div>';

	}

	public static function onLiquiFlowNetworkNotice( ) {


		global $wgDBname;
		global $wgOut;
		global $wgScriptPath;
		global $wgTitle;

		$dbr = wfGetDB( DB_REPLICA, '', $wgDBname );
		$tablename = 'networknotice';

		$categories = $wgOut->getCategories();

		$namespace = MWNamespace::getCanonicalName( $wgOut->getTitle()->getNamespace() );

		$wiki = substr( $wgScriptPath, 1 );  //Remove leading '/'

		$pagetitle = $wgTitle->getFullText();

		$action = '';

		//do wiki and namespace checks in DB query
		$res = $dbr->select( $tablename, array( 'notice_text', 'bgcolor', 'bordercolor', 'wiki', 'category', 'prefix' ), '(`namespace` = "' . $namespace . '" OR `namespace` = "") AND (`wiki` = "' . $wiki . '" OR `wiki` = "")' );



		foreach ( $res as $row ) {
			//If prefix doesnt match, go to next row/notice
			if( strncmp( $pagetitle, $row->{'prefix'}, strlen( $row->{'prefix'} ) ) ){ 
				continue;
			}

			//finally, check categories
			if( $row->{'category'} == "" ) {
				self::echoNotice( $row );
			} else {
				foreach ( $categories as $category ) {
					if( $category == $row->{'category'} ) {
						self::echoNotice( $row );
						break;
					}
				}
			}
		}

		return true;

	}

}