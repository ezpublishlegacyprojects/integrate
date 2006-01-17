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


// Include the super class file
include_once( "kernel/classes/ezdatatype.php" );

// Define the name of datatype string
define( "EZ_DATATYPESTRING_EXTERNALOPTION", "externaloption" );


class ExternalOptionType extends eZDataType
{
  /*!
   Construction of the class, note that the second parameter in eZDataType 
   is the actual name showed in the datatype dropdown list.
  */
  function ExternalOptionType()
  {
    $this->eZDataType( EZ_DATATYPESTRING_EXTERNALOPTION, "External Option" );
  }

  /*!
    Validates the input and returns true if the input was
    valid for this datatype.
  */
  function validateObjectAttributeHTTPInput( &$http, $base, 
                                               &$contentObjectAttribute )
  {
    return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
  }

  function deleteStoredObjectAttribute( &$contentObjectAttribute, $version = null )
  {
    $contentObjectID = $contentObjectAttribute->ContentObjectID;
  }


 /*!
 */

   function fetchObjectAttributeHTTPInput( &$http, $base, &$contentObjectAttribute )
   {
     if ( $http->hasPostVariable( $base . "_data_int_" . 
                                  $contentObjectAttribute->attribute( "id" ) ) )
     {
       $data =& $http->postVariable( $base . "_data_int_" . 
                                     $contentObjectAttribute->attribute( "id" ) 
                                   );
       $contentObjectAttribute->setAttribute( "data_int", $data );
       return true;
     }
     return false;
   }

  /*!
   Store the content. Since the content has been stored in function 
   fetchObjectAttributeHTTPInput(), this function is with empty code.
  */
  function storeObjectAttribute( &$contentObjectattribute )
  {
  }

  /*!
   Returns the meta data used for storing search indices.
  */
  function metaData( $contentObjectAttribute )
  {
    $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
    //
    $table = $contentClassAttribute->attribute( 'data_text1' );
    $index = $contentClassAttribute->attribute( 'data_text2' );
    $name  = $contentClassAttribute->attribute( 'data_text3' );
    $db =& eZDB::instance();
    $query = "SELECT $name as label FROM $table WHERE $index = ".$contentObjectAttribute->attribute( "data_int" );
    $result = $db->arrayQuery($query);
    if (is_array($result) && count($result) == 1)
      return $result[0]['label'];
    else
      return '';
  }

  /*!
   Returns the text.
  */
  function title( &$contentObjectAttribute )
  {
    return $this->metaData($contentObjectAttribute);
  }

  function isIndexable()
  {
    return true;
  }

  function sortKey( &$contentObjectAttribute )
  {
    return $this->metaData($contentObjectAttribute);
  }
  
  function sortKeyType()
  {
    return 'string';
  }

  function hasObjectAttributeContent( &$contentObjectAttribute )
  {
    return is_numeric($contentObjectAttribute->attribute( "data_int" ));
  }
  

  /*!
   Returns the content.
  */
  function &objectAttributeContent( &$contentObjectAttribute )
  {
      $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
      //
      $table = $contentClassAttribute->attribute( 'data_text1' );
      $index = $contentClassAttribute->attribute( 'data_text2' );
      $name  = $contentClassAttribute->attribute( 'data_text3' );
      $db =& eZDB::instance();
      $query = "SHOW COLUMNS FROM $table";
      $result = $db->arrayQuery($query);
      $order = 'ORDER BY label';
      foreach ($result as $row)
      {
        if ($row['Field'] == 'order')
        {
          $order = 'ORDER BY `order`';
          break;
        }
      }
      $query = "SELECT $index as val, $name as label FROM $table ".$order;
      $result = $db->arrayQuery($query);
      $output['options'] =& $result;
      $output['value'] =& $contentObjectAttribute->attribute( "data_int" );
      return $output;
  }

  function &classAttributeContent(&$classAttribute)
  {
    $table = $classAttribute->attribute( 'data_text1' );
    $index = $classAttribute->attribute( 'data_text2' );
    $name  = $classAttribute->attribute( 'data_text3' );
    $db =& eZDB::instance();
      $query = "SHOW COLUMNS FROM $table";
      $result = $db->arrayQuery($query);
      $order = 'ORDER BY label';
      foreach ($result as $row)
      {
        if ($row['Field'] == 'order')
        {
          $order = 'ORDER BY `order`';
          break;
        }
      }
      $query = "SELECT $index as val, $name as label FROM $table ".$order;
    $result = $db->arrayQuery($query);
    $output['options'] =& $result;
    return $output;
  }


    /*!
     Sets the default value.
    */

/*
 Not sure I need this
*/
  function initializeObjectAttribute( &$contentObjectAttribute, $currentVersion, &$originalContentObjectAttribute )
  {
    if ( $currentVersion == false )
    {
      $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
      //
      $table = $contentClassAttribute->attribute( 'data_text1' );
      $index = $contentClassAttribute->attribute( 'data_text2' );
      $name  = $contentClassAttribute->attribute( 'data_text3' );
      $db =& eZDB::instance();
      $query = "SELECT $index as val, $name as label FROM $table ORDER BY label";
      $result = $db->arrayQuery($query);
      $contentObjectAttribute->setContent( $result );
    }
  }


  function fetchClassAttributeHTTPInput( &$http, $base, &$classAttribute )
  {
    $defaultValueTable = $base . '_externaloption_table_'  . $classAttribute->attribute( 'id' );
    $defaultValueIndex = $base . '_externaloption_index_'  . $classAttribute->attribute( 'id' );
    $defaultValueName = $base . '_externaloption_name_'  . $classAttribute->attribute( 'id' );
    $returnvalue = false;
    if ( $http->hasPostVariable( $defaultValueTable ) )
    {
      $defaultValueTableValue = $http->postVariable( $defaultValueTable);

      if ($defaultValueTableValue == "")
      {
        $defaultValueTableValue = "";
      }
      $classAttribute->setAttribute( 'data_text1', $defaultValueTableValue );
      $returnvalue=true;
    }

    if ( $http->hasPostVariable( $defaultValueIndex ) )
    {
      $defaultValueIndexValue = $http->postVariable( $defaultValueIndex);

      if ($defaultValueIndexValue == "")
      {
        $defaultValueIndexValue = "";
      }
      $classAttribute->setAttribute( 'data_text2', $defaultValueIndexValue );
      $returnvalue=true;
    }

    if ( $http->hasPostVariable( $defaultValueName ) )
    {
      $defaultValueNameValue = $http->postVariable( $defaultValueName);

      if ($defaultValueNameValue == "")
      {
        $defaultValueNameValue = "";
      }
      $classAttribute->setAttribute( 'data_text3', $defaultValueNameValue );
      $returnvalue=true;
    }

    return $returnvalue;
  }



}
eZDataType::register( EZ_DATATYPESTRING_EXTERNALOPTION, "externaloptiontype" );
