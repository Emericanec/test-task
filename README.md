The idea is to spend **maximum of an hour**. Try to do as much as you can within this time. It does not mean that you have to finish the whole task, just try to do as much as possible.

You may spent some more time if required, but try not to.

## Task

Application should have a CLI tool to register new application (website), input information: website name and website URL. As the output, it should give API key. This key will be used to authenticate.

It should have the following endpoints:
- GET /user/{id} - get user information
- PUT /user/{id} - update user information
- DELETE /user/{id} - delete existing user
- POST /user - create new user

All of these endpoints should be protected by granted application token granted via CLI tool (non public).

When creating new user, we should keep the following information: first name, last name, email and to what application it is related to.  Email and application should be unique. Also we should know when entry was created, updated or soft deleted. Date representation is up to you.

It should be possible to create child user and attach him to existing user. Maximum nested level is 1, meaning you can create parent user and create child for it. It shouldn’t be possible to create child user for a child user.

Update endpoint should allow to change first and last name only.

Delete endpoint should soft delete a user, without actually deleting any data from the database.

Bonus points:
Write at least a few unit tests to cover business logic