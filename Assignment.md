## Time Spend on each section

### Backend (4h 10m):
    5m    - Creation of model blueprints via artisan. Verification/testing.
    15m   - Setup relations and migrations. Verification/testing.
    1h25m - Controller logic for the dailySummary dataset. Used by the AccMovementSummary page.
    10m   - Refactoring of the dailySummary function (PHP Storm made this easier than expected).
    1h45m - Creation/testing of seed files (Laravel database seed files).
    30m   - Unit tests for the movement summary page.

### Frontend (45m): 
    15m - Account listing page. Logic that handles moving to the AccMovementSummary page by clicking on a button.
    25m - Creation of the AccMovementSummary page for an account in tabular format. Logic to generate data structure is done via the dailySummary function.
    5m  - AccMovementSummary page updates with new date range selections.

### Trade-offs & architecture:
- Model relations
  - One to many (User -> Accounts)
  - One to many (Account -> Account_transactions)
  - One to one (Account_transaction -> Account)
- Trade-offs:
  - Saving (int)closing_balance on the account transaction table:
    - Not having any reference to account closing/opening balance at the time of the transaction would make it computationally expensive to calculate the value.
    - The tradeoff: lower computational complexity vs greater storage requirements.
  - Compound index on the account_transaction table. Columns account_id & created_at.
    - Same tradeoff analogy as the above point.
- Optimizations:
  - Compound index on the account_transaction table.
  - [Chunking Using Lazy Collections](https://laravel.com/docs/10.x/eloquent#chunking-using-lazy-collections).

### What would I improve on given more time:
- Implement infinite loader for the AccMovementSummary page. This will be beneficial for the following:
  - We can ensure with the help controller to load a smaller processed result set.
  - If the user scrolls to the bottom a loader can be implemented to load the i.e next 10 days.
  - Note, the date range from the datepicker will still be in effect but the front end will pull smaller chunks of the data.
