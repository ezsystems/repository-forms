Feature: Edit content
    In order to allow users to create content
    As a project owner
    I need to expose a form that creates a content item without using a draft.

Scenario: Create a folder without a draft
    Given there is a Content Type "folder" with the id "1"
      #And there is a Location with the id "2"
     When I go to "content/create/nodraft/1/eng-GB/2"
     Then I should see a content edit form

