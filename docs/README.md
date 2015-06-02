ReplaceTableRecord
===
Extension for Contao CMS

Copyright (c) 2015 de la Haye

---

Just use normal forms from the Contao form generator to save data. Normally Contao always(!) creates a new record on saving. This extension allows overwriting existing table records by using some insert tags.


### Usage

The usage is quite eays and not attached to any other extension. Every form of the form generator can be used for a simple way of "frontend editing".
- Create a form with the data fields that should be modified. The form has to be marked as "Save data to table" - the chosen table like one used by MetaModels.
- The form needs an additional field named "id". We can do this because Contao tables always have a field named "id".
- To pre-fill the form fields, the page has to be loaded with a parameter that specifies the record. E.g. in MetaModels you would use mypage/alias/myalias.html, in other ways maybe mypage/id/123.html.
- The "id"-field is filled by the insert-tags:
 - {{tabledata::id::TABLENAME::PARAMETERFIELD::URLPARAMETER}}
 - or {{tabledata::id::TABLENAME::PARAMETERFIELD::URLPARAMETER::member::MEMBERFIELD}} for restricting the record to a member stored in the table field MEMBERFIELD (always the id).
 - or {{tabledata::id::TABLENAME::PARAMETERFIELD::URLPARAMETER::groups::GROUPS}} for restricting the record to member groups. GROUPS can be a comma separated list like "1,2,3" or a serialized field. BUT: The way like e.g. the MetaModels store this data (spread tables) does not work! The data about the groups has to be in exact 1 field.
- The other fields are filled with the insert-tag:
 - {{tabledata::FIELDNAME::TABLENAME::PARAMETERFIELD::URLPARAMETER}}
 - e.g.: {{tabledata:firstname::mm_mydata::alias::person}} means "select firstname from mm_mydata where alias is equal the GET-value of person"
- The insert-tags are to be placed as the standard value for the form field.
- One thing about deleting a record: Just place onle the "id"-field in a form to delete the chosen entry instead of saving it.
- Additionally there is a hook "dlh_ReplaceTableRecord" in the ReplaceRecord.php which allows you to integrate your own rules on deciding when to overwrite or not.

Feel free to experiment with it. Maybe a MetaModel-List and a page to modify the record. You can also build a thing that just allows the modification of an own entry by the current member, but no deletion. There are a bunch of opportunities to use this little feature.

That's it. Have fun ;)
