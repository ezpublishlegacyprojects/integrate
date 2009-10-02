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
//include_once( "kernel/classes/ezdatatype.php" );


class ExternalMultipleOptionType extends eZDataType
{
  // Define the name of datatype string
  const DATA_TYPE_STRING = "externalmultipleoption";

  /*!
   Construction of the class, note that the second parameter in eZDataType
   is the actual name showed in the datatype dropdown list.
  */
  function ExternalMultipleOptionType()
  {
    $this->eZDataType( ExternalMultipleOptionType::DATA_TYPE_STRING, "External Multiple Option" );
  }

  /*!
    Validates the input and returns true if the input was
    valid for this datatype.
  */
  function validateObjectAttributeHTTPInput( $http, $base,
                                               $contentObjectAttribute )
  {
    return eZInputValidator::STATE_ACCEPTED;
  }

  function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
  {
    $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
    $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
    $index    = $contentClassAttribute->attribute( 'data_text2' );
    $storage  = $contentClassAttribute->attribute( 'data_text4' );
    $version_sql ='';
    if ($version != null)
      $version_sql = " AND version = $version";
    $query = "DELETE FROM $storage WHERE contentobject_attribute_id = $contentObjectAttributeID" . $version_sql;
    $db = eZDB::instance();
    $result = $db->query($query);
  }

 /*!
 */

   function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
   {
     if ( $http->hasPostVariable( $base . "_data_int_" .
                                  $contentObjectAttribute->attribute( "id" ) ) )
     {
       $data = $http->postVariable( $base . "_data_int_" .
                                     $contentObjectAttribute->attribute( "id" )
                                   );
       $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
       $contentObjectID = $contentObjectAttribute->attribute( 'contentobject_id' );
       $version = $contentObjectAttribute->attribute( "version" );

       $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
       $index    = $contentClassAttribute->attribute( 'data_text2' );
       $storage  = $contentClassAttribute->attribute( 'data_text4' );
       $db = eZDB::instance();
       if (is_array($data))
       {
         foreach ($data as $val)
         {
           $query = "INSERT INTO $storage (contentobject_id, contentobject_attribute_id, version, $index) VALUES ($contentObjectID, $contentObjectAttributeID, $version, $val)";
           $result = $db->query($query);
         }
       }
       $contentObjectAttribute->setAttribute( "data_int", $version );
       return true;
     }
     return false;
   }

  /*!
   Store the content. Since the content has been stored in function
   fetchObjectAttributeHTTPInput(), this function is with empty code.
  */
  function storeObjectAttribute( $contentObjectattribute )
  {
  }

  /*!
   Returns the meta data used for storing search indices.
  */
  function metaData( $contentObjectAttribute )
  {
    $returnArray=array();

    if ($contentObjectAttribute)
    {
      $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
      $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
      $version = $contentObjectAttribute->attribute( "data_int" );
      //
      $table    = $contentClassAttribute->attribute( 'data_text1' );
      $index    = $contentClassAttribute->attribute( 'data_text2' );
      $name     = $contentClassAttribute->attribute( 'data_text3' );
      $storage  = $contentClassAttribute->attribute( 'data_text4' );

      $db = eZDB::instance();
      $query = "SELECT $index as val, $name as label FROM $table ORDER BY label";
      $result = $db->arrayQuery($query);
      $options = array();
      foreach ($result as $row)
      {
        $options[$row['val']]=$row['label'];
      }

      $returnArray=array();
      if (is_numeric($contentObjectAttributeID) && is_numeric($version))
      {
        $query = "SELECT $index as val FROM $storage WHERE contentobject_attribute_id = $contentObjectAttributeID AND version = $version";
        $dbresult = $db->arrayQuery($query);
        foreach ($dbresult as $row)
        {
          $returnArray[]=$options[$row['val']];
        }
      }
    }
    return join(' ',$returnArray);
  }



  /*!
   Returns the text.
  */
  function title( $contentObjectAttribute, $name=null )
  {
    return $this->metaData($contentObjectAttribute);
  }

  function isIndexable()
  {
    return true;
  }

  function sortKey( $contentObjectAttribute )
  {
    return $this->metaData($contentObjectAttribute);
  }

  function sortKeyType()
  {
    return 'string';
  }

  function hasObjectAttributeContent( $contentObjectAttribute )
  {
    return ($this->metaData($contentObjectAttribute) != '');
  }

  /*!
   Returns the content.
  */
  function objectAttributeContent( $contentObjectAttribute )
  {
      $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
      $contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
      $version = $contentObjectAttribute->attribute( "data_int" );
      //
      $table    = $contentClassAttribute->attribute( 'data_text1' );
      $index    = $contentClassAttribute->attribute( 'data_text2' );
      $name     = $contentClassAttribute->attribute( 'data_text3' );
      $storage  = $contentClassAttribute->attribute( 'data_text4' );

      $db = eZDB::instance();
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
      $output['options'] = $result;

      $query = "SELECT $index as val FROM $storage WHERE contentobject_attribute_id = $contentObjectAttributeID AND version = $version";
      $dbresult = $db->arrayQuery($query);
      $values = array();
      foreach ($dbresult as $row)
      {
        $values[]=$row['val'];
      }
      $output['value'] = $values;
      return $output;
  }

  function classAttributeContent($classAttribute)
  {
    $table = $classAttribute->attribute( 'data_text1' );
    $index = $classAttribute->attribute( 'data_text2' );
    $name  = $classAttribute->attribute( 'data_text3' );
    $db = eZDB::instance();
    $query = "SELECT $index as val, $name as label FROM $table ORDER BY label";
    $result = $db->arrayQuery($query);
    $output['options'] = $result;
    return $output;
  }



  function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
  {
     // TODO: check that tables & cols exist
    $defaultValueTable   = $base . '_externalmultipleoption_table_'   . $classAttribute->attribute( 'id' );
    $defaultValueIndex   = $base . '_externalmultipleoption_index_'   . $classAttribute->attribute( 'id' );
    $defaultValueName    = $base . '_externalmultipleoption_name_'    . $classAttribute->attribute( 'id' );
    $defaultValueStorage = $base . '_externalmultipleoption_storage_' . $classAttribute->attribute( 'id' );
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

    if ( $http->hasPostVariable( $defaultValueStorage ) )
    {
      $defaultValueStorageValue = $http->postVariable( $defaultValueStorage);

      if ($defaultValueStorageValue == "")
      {
        $defaultValueStorageValue = "";
      }
      $classAttribute->setAttribute( 'data_text4', $defaultValueStorageValue );
      $returnvalue=true;
    }

    return $returnvalue;
  }



}
eZDataType::register( ExternalMultipleOptionType::DATA_TYPE_STRING, "externalmultipleoptiontype" );

?>
