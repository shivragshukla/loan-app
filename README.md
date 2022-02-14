# Laravel API for Loan Application
[![GitHub issues](https://img.shields.io/github/issues/shivragshukla/loan-app)](https://github.com/shivragshukla/loan-app/issues)
[![GitHub license](https://img.shields.io/github/license/shivragshukla/loan-app)](https://github.com/shivragshukla/loan-app/blob/master/LICENSE)

This will create authenticate a user, who will apply for a loan and repay the loan weekly & save in database.
For late repay, Amount will deducted from repay & saved in Penalty.
Admin will check the loans & approve/reject accordinly.


## Installation

### Step 1
Clone the application via git. 
```bash
git clone https://github.com/shivragshukla/loan-app.git

```

#### Package install

Run the command : 

- The Laravel package will automatically register itself, so you can start using it immediately.

```shell
composer install
```

#### Key generate

Run the command : 

- The Laravel key generate immediately.

```shell
php artisan key:generate
```

### Step 2 - SetUp database

- Open .env file set DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Run the command : 

```shell
composer dump-autoload
```

```shell
php artisan migrate:refresh --seed
```

-The jwt token package will automatically register itself, so you can start using it immediately.

```shell
php artisan jwt:secret
```

## Usage

- Run the command on 1st shell: 

```shell
php artisan serve
```

- Run the command on 2nd shell: 


#### Username & password

- Username : admin@admin.com
- Password : 123456


## Screenshot

#### Registration
![register](https://user-images.githubusercontent.com/30346330/153858707-d837190a-c6c9-4b11-9947-4296a4ad1c74.png)

#### Login
![login](https://user-images.githubusercontent.com/30346330/153858699-9841c2ad-bf4b-458f-9649-569015571001.png)

#### Profile
![profile](https://user-images.githubusercontent.com/30346330/153858704-0331a1b4-d690-4d84-860f-866a07b83764.png)

#### Loan apply
![loan-apply](https://user-images.githubusercontent.com/30346330/153858684-2bba0663-4a07-4ac2-8255-ae95cc41a319.png)

#### Loan approve
![loan-approve](https://user-images.githubusercontent.com/30346330/153858688-f6e1c92d-268f-4c31-8bcb-899d2b3be529.png)

#### Loan - getAll
![loan-get-all](https://user-images.githubusercontent.com/30346330/153858692-baa7e782-af13-413c-9697-eab40608d1a1.png)

#### Loan - summary
![summary](https://user-images.githubusercontent.com/30346330/153858713-c3cc1a4c-75a1-4ad2-ae5d-856ede480edb.png)


#### Repayment
![repayment](https://user-images.githubusercontent.com/30346330/153858710-3dab3906-d488-4b8b-8eb8-92211e593d25.png)


Development supported by: Shivrag Shukla
<br>
For any doubts contact : shivragshukla001@gmail.com
