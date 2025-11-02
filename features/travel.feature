# features/travel.feature

  Feature:

    Scenario: Call a API to get Travels
      When call API to get locations from travel to "aaaaa"
      Then the response status code should be 200


