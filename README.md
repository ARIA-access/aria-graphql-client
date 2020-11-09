# ARIA GraphQL API

Provides a PHP Library for communicating with the ARIA GraphQL API.


## Installation

Add the repository to your composer.json:

```
"repositories": [
    {
      "type": "vcs",
      "url": "https://gitlab.com/aria-php/aria-graphql-client.git"
    }
  ],
```

and then `composer require aria-php/aria-graphql-client` from within your project.

## Usage

The `Client` provides the low level interface for calling the GraphQL server, however 
you should use one of the interface classes based off of `APIDefinition`.

These interface classes do / will provide a function level interface for the API. E.g.

```

$client = new Client();
$client->setToken($bearer);

$documents = new DocumentAPI($client);

$docs = $documents->getDocuments();

```

