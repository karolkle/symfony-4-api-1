Feature: Manage users
  @createSchema
  Scenario: Create user

    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/users" with body:
    """
    {

    "firstName": "Karol",
    "lastName": "Kle",
    "gender": "male",
    "dateOfBirth": "1991-05-11",
    "city": "Warszawa",
    "country": "PL",
    "nationality": "PL",
    "email": "Karol@kar.pl",
    "username": "Karolkar",
    "phone": "564665344"

    }
    """



