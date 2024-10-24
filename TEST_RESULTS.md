# Test Results

This document contains the latest test results for the Bookstore Management System.

## Last Test Run

```
   PASS  Tests\Feature\AuthTest
  ✓ user can register                                                                                                                                                                              0.25s  
  ✓ user can login                                                                                                                                                                                 0.04s  
  ✓ user cannot login with incorrect password                                                                                                                                                      0.22s  
  ✓ authenticated user can logout                                                                                                                                                                  0.03s  
  ✓ registration fails with invalid email format                                                                                                                                                   0.02s  
  ✓ registration fails with duplicate email                                                                                                                                                        0.01s  
  ✓ registration fails with short password                                                                                                                                                         0.01s  
  ✓ registration fails with invalid role                                                                                                                                                           0.01s  
  ✓ user can logout and token is deleted                                                                                                                                                           0.01s  
  ✓ unauthorized access after logout                                                                                                                                                               0.01s  

   PASS  BookTest
  ✓ unauthenticated user cannot create book                                                                                                                                                        0.04s  
  ✓ unauthenticated user cannot update book                                                                                                                                                        0.03s  
  ✓ unauthenticated user cannot delete book                                                                                                                                                        0.02s  
  ✓ regular user cannot create book                                                                                                                                                                0.03s  
  ✓ regular user cannot update book                                                                                                                                                                0.02s  
  ✓ regular user cannot delete book                                                                                                                                                                0.02s  
  ✓ admin user can create book                                                                                                                                                                     3.32s  
  ✓ admin user can update book                                                                                                                                                                     0.03s  
  ✓ admin user can delete book                                                                                                                                                                     0.02s  
  ✓ create book with valid data succeeds                                                                                                                                                           1.06s  
  ✓ create book with invalid data fails                                                                                                                                                            0.05s  
  ✓ get all books returns paginated list                                                                                                                                                           0.03s  
  ✓ get single book returns book data                                                                                                                                                              0.03s  
  ✓ get non existing book returns 404                                                                                                                                                              0.02s  
  ✓ update book with valid data succeeds                                                                                                                                                           0.02s  
  ✓ update book with invalid data fails                                                                                                                                                            0.02s  
  ✓ update non existing book returns 404                                                                                                                                                           0.02s  
  ✓ delete existing book succeeds                                                                                                                                                                  0.02s  
  ✓ delete non existing book returns 404                                                                                                                                                           0.02s  
  ✓ search books by title returns matching books                                                                                                                                                   0.02s  
  ✓ search books by author returns matching books                                                                                                                                                  0.02s  
  ✓ search books by isbn returns matching books                                                                                                                                                    0.02s  
  ✓ search with no matching books returns empty result                                                                                                                                             0.02s  
  ✓ pagination returns correct number of books per page                                                                                                                                            0.02s  
  ✓ pagination returns correct meta data                                                                                                                                                           0.03s  
  ✓ title is required when creating book                                                                                                                                                           0.02s  
  ✓ isbn must be 13 characters long                                                                                                                                                                0.02s  

   PASS  DatabaseTest
  ✓ can create book record                                                                                                                                                                         0.03s  
  ✓ cannot create book with missing required fields                                                                                                                                                0.02s  
  ✓ can retrieve book by id                                                                                                                                                                        0.02s  
  ✓ can retrieve all books                                                                                                                                                                         0.02s  
  ✓ retrieving non existing book returns null                                                                                                                                                      0.02s  
  ✓ can update book attributes                                                                                                                                                                     0.02s  
  ✓ cannot update book with missing required fields                                                                                                                                                0.02s  
  ✓ can delete book                                                                                                                                                                                0.02s  
  ✓ deleting non existing book does not throw exception                                                                                                                                            0.02s  
  ✓ user belongs to a role                                                                                                                                                                         0.02s  
  ✓ can retrieve user role                                                                                                                                                                         0.02s  
  ✓ can filter books by title                                                                                                                                                                      0.02s  
  ✓ can sort books by title ascending                                                                                                                                                              0.03s  
  ✓ cannot create book with duplicate isbn                                                                                                                                                         0.02s  
  ✓ cannot create user with duplicate email                                                                                                                                                        0.02s  
  ✓ database transactions rollback on failure                                                                                                                                                      0.02s  

  Tests:    53 passed (211 assertions)
  Duration: 6.02s

```
