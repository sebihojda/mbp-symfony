Prepend CSV header, piping compatible:

```
> cat csv/header-prepend.csv | ./console app:csv:prepend-header --headers name,age,occupation,location
```

or with a short option for headers:

```
> cat csv/header-prepend.csv | ./console app:csv:prepend-header -t name,age,occupation,location
```

Merge CSVs with inputs/output given as options:

```
> ./console app:csv:merge csv/merge1.csv csv/merge2.csv --output csv/merged.csv
```

or with inputs as options and output to STDOUT:

```
> ./console app:csv:merge csv/merge1.csv csv/merge2.csv
```

Merge headerless tables, then prepend headers:

```
 ./console app:csv:merge csv/merge_prepend1.csv csv/merge_prepend2.csv --no-headers | ./console app:csv:prepend-header --headers name,age,occupation,location
```

For generating PEM keys, you would use:
```
openssl genpkey -algorithm RSA -out keys/private_key.pem -pkeyopt rsa_keygen_bits:2048
openssl rsa -pubout -in keys/private_key.pem -out keys/public_key.pem
```
But they are already provided in `keys` dir.

To sign a CSV file column:
```
cat csv/to_sign.csv | ./console app:csv:column-sign -c grade --private-key keys/private_key.pem > csv/signed.csv
```
Then, without altering signed.csv:
```
cat csv/signed.csv | ./console app:csv:column-sign-verify -c grade -k 'keys/public_key.pem'
```
The out will be the original CSV data, just as `to_sign.csv`.


But if the contents of the `grade` column of the rows in `signed.csv` is altered and:
```
cat csv/signed.csv | ./console app:csv:column-sign-verify -c grade -k 'keys/public_key.pem'
```
Will output something such as:
```
Invalid signature for row Dan, 38, Cluj-Napoca, 9.6, TK1flz7qR4ImowrWqQTk3SgEJL7Ptwz9edpS6s0
6qUSABpLbdEXeGUo/wzsWe2HkxsF5HHNVjV6klOAJus0I0Qki+8FXV1X1KuNztav3kx+nXn4kt3S+MhFIWVj4AKWDmEY
TGUdwe7ptVjzOHYWYLbLIpLqhEzU3dceKt51IUJa+WtIUD7fV0xQHYI/VVbvgQFNMGRpZzoaNzs4Xi7uNX7qWyOn9/f0
SyztvcXkkH+nUKHCHEIQqd9N6857h4+lYpVDg0YhP3+1twzYFgfg0AMF9G3U/vEkGFLYgKzO+Pn2Gm6VgW0Jt7MYeW7TgUdqz3EzXa6Yw1i10pnnlFGCuoA==
```

Join CSV files similarly to RDBMS inner join (join two CSVs based on equality of values in two related columns), output the joined CSV with all columns :
```
./console app:csv:inner-join csv/users.csv csv/orders.csv --left-on=user_id --right-on=customer_id --output=csv/joined_data.csv
```
```
./console app:csv:inner-join csv/users.csv csv/orders.csv --left-on=user_id --right-on=customer_id
```

Select columns and apply where clauses (equality, less than, greater than, like regexp), output the CSV with selected columns and corresponding rows:

```
./console app:csv:select csv/users.csv --select=name,city
```

```
./console app:csv:select csv/users.csv --where="user_id > 2"
```

```
./console app:csv:select csv/users.csv --select=name --where="city = Bucuresti"
```

```
./console app:csv:select csv/users.csv --where="name ~ /Popescu$/"
```

Encrypt/Decrypt values in certain column(s) with provided keys, using asymmetric encryption:

```
cat csv/sensitive_data.csv | ./console app:csv:column-encrypt --column=credit_card --public-key=keys/public_key.pem > csv/encrypted_data.csv
```

```
cat csv/encrypted_data.csv | ./console app:csv:column-decrypt --column=credit_card --private-key=keys/private_key.pem
```
