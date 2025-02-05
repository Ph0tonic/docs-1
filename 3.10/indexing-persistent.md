---
layout: default
description: It is possible to define a persistent index on one or more attributes (or paths) of documents
---
Persistent indexes
==================

It is possible to define a persistent index on one or more document attributes (or paths).
The index is then used in queries to locate documents with a specific index attribute value
or to find documents whose index attribute value(s) are in a given range. 

Since ArangoDB 3.10, a persistent index can store additional attributes that can be used
for projections, but not for index lookups or sorting. This allows a persistent index to
fully cover more queries and avoid extra document lookups. These extra attributes can be
defined by setting the `storedValues` attribute of the index. Non-existing attributes are 
stored as **null** values inside `storedValues`. The maximum number of attributes in 
`storedValues` is 32.

If the index is declared unique, then no two documents are allowed to have the same 
set of attribute values. Creating a new document or updating a document will fail if the 
uniqueness is violated. Only the index attributes in the `fields` are checked for uniqueness,
but the attributes in `storedValues` are not checked for their uniqueness.

If the index is declared sparse, a document will be excluded from the index and no 
uniqueness checks will be performed if any index attribute value is not set or has a value 
of `null`. 

It is not possible to create multiple persistent indexes with the same `fields` attributes
and uniqueness but different `storedValues` attributes. That means the value of 
`storedValues` is not considered in index creation calls when checking if a persistent
index is already present or needs to be created.


Accessing Persistent Indexes from the Shell
-------------------------------------------

Ensures that a unique persistent index exists:

`collection.ensureIndex({ type: "persistent", fields: [ "field1", ..., "fieldn" ], unique: true })`

Creates a unique persistent index on all documents using *field1*, ... *fieldn*
as attribute paths. At least one attribute path has to be given. The index will
be non-sparse by default.

All documents in the collection must differ in terms of the indexed 
attributes. Creating a new document or updating an existing document will
will fail if the attribute uniqueness is violated. 

To create a sparse unique index, set the *sparse* attribute to `true`:

`collection.ensureIndex({ type: "persistent", fields: [ "field1", ..., "fieldn" ], unique: true, sparse: true })`

In a sparse index all documents will be excluded from the index that do not 
contain at least one of the specified index attributes or that have a value 
of `null` in any of the specified index attributes. Such documents will
not be indexed, and not be taken into account for uniqueness checks.

In a non-sparse index, these documents will be indexed (for non-present
indexed attributes, a value of `null` will be used) and will be taken into
account for uniqueness checks.

In case that the index was successfully created, an object with the index
details, including the index-identifier, is returned.

{% arangoshexample examplevar="examplevar" script="script" result="result" %}
    @startDocuBlockInline ensureUniquePersistentSingle
    @EXAMPLE_ARANGOSH_OUTPUT{ensureUniquePersistentSingle}
    ~db._create("ids");
    db.ids.ensureIndex({ type: "persistent", fields: [ "myId" ], unique: true });
    db.ids.save({ "myId": 123 });
    db.ids.save({ "myId": 456 });
    db.ids.save({ "myId": 789 });
    db.ids.save({ "myId": 123 });  // xpError(ERROR_ARANGO_UNIQUE_CONSTRAINT_VIOLATED)
    ~db._drop("ids");
    @END_EXAMPLE_ARANGOSH_OUTPUT
    @endDocuBlock ensureUniquePersistentSingle
{% endarangoshexample %}
{% include arangoshexample.html id=examplevar script=script result=result %}

{% arangoshexample examplevar="examplevar" script="script" result="result" %}    
    @startDocuBlockInline ensureUniquePersistentMultiColmun
    @EXAMPLE_ARANGOSH_OUTPUT{ensureUniquePersistentMultiColmun}
    ~db._create("ids");
    db.ids.ensureIndex({ type: "persistent", fields: [ "name.first", "name.last" ], unique: true });
    db.ids.save({ "name" : { "first" : "hans", "last": "hansen" }});
    db.ids.save({ "name" : { "first" : "jens", "last": "jensen" }});
    db.ids.save({ "name" : { "first" : "hans", "last": "jensen" }});
    db.ids.save({ "name" : { "first" : "hans", "last": "hansen" }});  // xpError(ERROR_ARANGO_UNIQUE_CONSTRAINT_VIOLATED)
    ~db._drop("ids");
    @END_EXAMPLE_ARANGOSH_OUTPUT
    @endDocuBlock ensureUniquePersistentMultiColmun
{% endarangoshexample %}
{% include arangoshexample.html id=examplevar script=script result=result %}


<!-- js/server/modules/@arangodb/arango-collection.js-->


Ensures that a non-unique persistent index exists:

`collection.ensureIndex({ type: "persistent", fields: [ "field1", ..., "fieldn" ] })`

Creates a non-unique persistent index on all documents using *field1*, ...
*fieldn* as attribute paths. At least one attribute path has to be given.
The index will be non-sparse by default.

To create a sparse unique index, set the *sparse* attribute to `true`.

In case that the index was successfully created, an object with the index
details, including the index-identifier, is returned.

{% arangoshexample examplevar="examplevar" script="script" result="result" %}
    @startDocuBlockInline ensurePersistent
    @EXAMPLE_ARANGOSH_OUTPUT{ensurePersistent}
    ~db._create("names");
    db.names.ensureIndex({ type: "persistent", fields: [ "first" ] });
    db.names.save({ "first" : "Tim" });
    db.names.save({ "first" : "Tom" });
    db.names.save({ "first" : "John" });
    db.names.save({ "first" : "Tim" });
    db.names.save({ "first" : "Tom" });
    ~db._drop("names");
    @END_EXAMPLE_ARANGOSH_OUTPUT
    @endDocuBlock ensurePersistent
{% endarangoshexample %}
{% include arangoshexample.html id=examplevar script=script result=result %}

### Query by example using a persistent index

Constructs a query-by-example using a persistent index:

`collection.byExample(example)`

Selects all documents from the collection that match the specified example 
and returns a cursor. A persistent index will be used if present.

You can use *toArray*, *next*, or *hasNext* to access the
result. The result can be limited using the *skip* and *limit*
operator.

An attribute name of the form *a.b* is interpreted as attribute path,
not as attribute. If you use

```json
{ "a" : { "c" : 1 } }
```

as example, then you will find all documents, such that the attribute
*a* contains a document of the form *{c : 1 }*. For example the document

```json
{ "a" : { "c" : 1 }, "b" : 1 }
```

will match, but the document

```json
{ "a" : { "c" : 1, "b" : 1 } }
```

will not.

However, if you use

```json
{ "a.c" : 1 },
```

then you will find all documents, which contain a sub-document in *a*
that has an attribute *c* of value *1*. Both the following documents

```json
{ "a" : { "c" : 1 }, "b" : 1 }
```
and

```json
{ "a" : { "c" : 1, "b" : 1 } }
```
will match.

Persistent Indexes and Server Language
--------------------------------------

The order of index entries in persistent indexes adheres to the configured
[server language](programs-arangod-general.html#default-language).
If, however, the server is restarted with a different language setting as when
the persistent index was created, not all documents may be returned anymore and
the sort order of those which are returned can be wrong (whenever the persistent
index is consulted).

To fix persistent indexes after a language change, delete and re-create them.
