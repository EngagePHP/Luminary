# JSON Api Query

The JSON Api Query service was developed to automatically handle JSON API spec request queries within Eloquent. The goal would be to receive the request, determine if the request method allows the request parameters and automatically apply them if applicable.

The formatting for the JSON Api Query service can be found [here](http://jsonapi.org/format/)

## Inclusion of Resources

Returning related resources for a parent resources can be done by added them to the url query:

`GET /customers/<id>?include=users,users.location`

This will will include users related to customers as well as the users location relation.

## Sparse Fieldsets

Requesting a restricted set of fields for a given resource type is as follows:

`GET /customers?include=users.users.location?fields[customers]=id,name&fields[users]=id,first_name,last_name&fields[users.location]=name,state,id`

## Sorting

You can sort resource collections as follows:

**Parent Collection**

`GET /customers?sort=name` *Will sort the customer by name*

`GET /customers?sort=-name` *Will sort by name DESC*

`GET /customers?sort=name,-zipcode` *Will sort by name ASC and zipcode DESC*

**Related Collection Sorting**

`GET /customers?include=users&sort=name,-zipcode,users.first_name,users.last_name`
This will sort customers based on name ASC and zipcode DESC, and their related users by first_name and last_name ASC

## Pagination

pagination will only affect the parent collection, and works as follows:

**Page-based pagination**
`GET /customers?page[number]=3&page[size]=15` *Will return 15 results from page 3*

**Offset-based pagination**

`Currenty not implemented`

**Cursor-based pagination**

`Currenty not implemented`

## Filtering

You can run filters in the request with a custom filtering strategy specifically for the API Query service

### Fundamentals of the filtering strategy

* If not specified, the service will always assume an `AND` query
* If operator not specified, the service will always assume an `=` operator
* Available filter types: `and`, `or`, `nested`
* Available operator types: `=`, `!=`, `<>`, `>`, `<`, `>=`, `<=`, `!<`, `!>`, `IN`, `NOT IN`, `NULL`, `NOT NULL`

### Basic Filtering

**Basic AND WHERE Filters**

`GET /customers?filter[]=id,1234` *Will filter the results based on customer with an id of 1234 and will assume an = operator*

`GET /customers?filter[and][]=created_at,>,03-21-2016` *Will filter customers created after a certain date*

**Basic OR WHERE Filters**

`GET /customers?filter[and][]=created_at,>,03-21-2016&filter[or][]=created_at,<,03-21-2017` *Will filter customers between a certain date*

**Basic WHERE Nested Filters**

Nested filters are equivalent to SQL clause like so: `WHERE (customers.created_at > '03-21-2016' || customers.created_at < '03-21-2017')`

*AND* `GET /customers?filter[nested][and][]=created_at,>,03-21-2016`

*OR* `GET /customers?filter[nested][and][]=created_at,>,03-21-2016&filter[nested][or][]=created_at,<=,03-21-2017`

### Advanced Filtering

Advanced is the recommended approach to filtering requests with the service. Although the parser can handle mix and match of styles,
the advanced filtering style is the most straightforward and easy to read.

**Advanced AND WHERE Filters**

`GET /customers?filter[customers]=id,1234` *Will filter the results based on customer with an id of 1234 and will assume an = operator*

`GET /customers?filter[customers][and][]=created_at,>,03-21-2016` *Will filter customers created after a certain date*

**Advanced OR WHERE Filters**

`GET /customers?filter[customers][and][]=created_at,>,03-21-2016&filter[customers][or][]=created_at,<,03-21-2017` *Will filter customers between a certain date*

**Advanced WHERE Nested Filters**

Nested filters are equivalent to SQL clause like so: `WHERE (customers.created_at > '03-21-2016' || customers.created_at < '03-21-2017')`

*AND* `GET /customers?filter[customers][nested][and][]=created_at,>,03-21-2016`

*OR* `GET /customers?filter[customers][nested][and][]=created_at,>,03-21-2016&filter[customers][nested][or][]=created_at,<=,03-21-2017`

### Relationship Filtering

Relationship filtering will require the advanced filtering format for each related filter.

`GET /customers?include=users,users.location&filter[customers]=id,1234&filter[users]=first_name,andrew`

This filter will query for a customer with an id of 1234 and only return related users with a first name of andrew
