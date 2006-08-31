<?php
//
// Copyright (C) 2006. designIT.  All rights reserved.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact license@designit.com.au if any conditions of this licencing isn't clear to
// you.
//


include_once( 'kernel/classes/ezcontentclassattribute.php' );

class externalMultipleOptionSearch
{
  /*!
   Constructor
  */
  function externalMultipleOptionSearch()
  {
    // Empty...
  }
 
  function createSqlParts( $params )
  {
    /* $params showld be a hash with 2 values 
       attribute_id - the attribute id of the externalMultipleOption attribute
       values       - an array of integers to match */
    $sqlTables = '';
    $sqlCond   = '';
    if ( isset($params['attribute_id']) &&
         isset($params['values']) && 
         is_array($params['values']) && 
         count($params['values']) > 0 && 
         is_numeric($params['values'][0])
       )
    {
      $class_attribute_id = $params['attribute_id'];
      $values = $params['values'];

      // Retrieve class attribute  and check that it's the right type
      $contentClassAttribute =& eZContentClassAttribute::fetch($class_attribute_id);
 
      if ( $contentClassAttribute && $contentClassAttribute->attribute('data_type_string') == 'externalmultipleoption')
      {
  
        eZDebug::writeDebug("externalMultipleOptionSearch: good data building query" );
        $index    = $contentClassAttribute->attribute( 'data_text2' );
        $name     = $contentClassAttribute->attribute( 'data_text3' );
        $storage  = $contentClassAttribute->attribute( 'data_text4' );
        $query = "SELECT DISTINCT ezcontentobject_attribute.contentobject_id, ezcontentobject_attribute.version
        FROM ezcontentobject_attribute, $storage
        WHERE ezcontentobject_attribute.contentclassattribute_id = $class_attribute_id
        AND ezcontentobject_attribute.contentobject_id = $storage.contentobject_id
        AND ezcontentobject_attribute.version = $storage.version
        AND $storage.$index
        IN ( ".join(',',$values).")";

        include_once( "lib/ezdb/classes/ezdb.php" );
        $db =& eZDB::instance();
        $result = $db->arrayQuery($query);
        $IDs = array();
        foreach ($result as $row)
        {
          $IDs[]="(".$row['contentobject_id'].','.$row['version'].')';
        }
        if (count($IDs) != 0 ) 
        {
          $sqlTables = ", ezcontentobject_attribute externalmultipleoptionSearch";

          $sqlJoinArray = array();
          $sqlJoinArray[] = "externalmultipleoptionSearch.contentobject_id = ezcontentobject.id";
          $sqlJoinArray[] = "externalmultipleoptionSearch.contentclassattribute_id = ".$class_attribute_id ;
          $sqlJoinArray[] = "externalmultipleoptionSearch.version = ezcontentobject_name.content_version";
  
          $sqlCondArray = array();
          $sqlCondArray[] = '(externalmultipleoptionSearch.contentobject_id,externalmultipleoptionSearch.version) IN ('.join(',',$IDs).')';
    
          $sqlCond = implode( " AND \n", $sqlJoinArray )." AND \n (".implode( " AND \n ", $sqlCondArray ) .') AND ';
        }
        else
        {
          $sqlCond = " 1 = -1 AND ";
        }
      }
      else
      {
        eZDebug::writeDebug("externalMultipleOptionSearch: bad data incorrect content class attribute" );
      }
    }
    else
    {
      eZDebug::writeDebug("externalMultipleOptionSearch:missing parameters" );
    }

    return array( 'tables' => $sqlTables, 'joins' => $sqlCond );
  }
}
?>
