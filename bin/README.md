Prepend CSV header, piping compatible:

```
cat csv/header-prepend.csv | ./csv-headers-prepend --headers name,age,occupation,location
```

```
cat csv/header-prepend.csv | ./csv-headers-prepend -h name,age,occupation,location
```

Merge CSVs with inputs/output given as options:

```
./csv-merge --input1 csv/merge1.csv --input2 csv/merge2.csv --output1 csv/merged.csv 
```

or with inputs as options and output to STDOUT:

```
./csv-merge --input1 csv/merge1.csv --input2 csv/merge2.csv
```

Merge headerless tables, then prepend headers:

```
./csv-merge --input1 csv/merge_prepend1.csv --input2 csv/merge_prepend2.csv --ignore-headers | ./csv-headers-prepend --headers name,age,occupation,location
```

For generating PEM keys, you would use:
```
openssl genpkey -algorithm RSA -out private_key.pem -pkeyopt rsa_keygen_bits:2048
openssl rsa -pubout -in private_key.pem -out public_key.pem
```
But they are already provided in `keys` dir.

To sign a CSV file column:
```
cat csv/to_sign.csv | ./csv-column-sign -c grade -k 'keys/private_key.pem' > csv/signed.csv
```
Then, without altering signed.csv:
```
cat csv/signed.csv | ./csv-column-sign-verify -c grade -k 'keys/public_key.pem'
```
The out will be the original CSV data, just as `to_sign.csv`.


But if the contents of the `grade` column of the rows in `signed.csv` is altered and:
```
cat csv/signed.csv | ./csv-column-sign-verify -c grade -k 'keys/public_key.pem'
```
Will output something such as:
```
Invalid signature for row Dan, 38, Cluj-Napoca, 9.6, TK1flz7qR4ImowrWqQTk3SgEJL7Ptwz9edpS6s0
6qUSABpLbdEXeGUo/wzsWe2HkxsF5HHNVjV6klOAJus0I0Qki+8FXV1X1KuNztav3kx+nXn4kt3S+MhFIWVj4AKWDmEY
TGUdwe7ptVjzOHYWYLbLIpLqhEzU3dceKt51IUJa+WtIUD7fV0xQHYI/VVbvgQFNMGRpZzoaNzs4Xi7uNX7qWyOn9/f0
SyztvcXkkH+nUKHCHEIQqd9N6857h4+lYpVDg0YhP3+1twzYFgfg0AMF9G3U/vEkGFLYgKzO+Pn2Gm6VgW0Jt7MYeW7TgUdqz3EzXa6Yw1i10pnnlFGCuoA==
```

Add an indexing column to a CSV file (column counting rows from 1 to ...):

```
cat csv/merged.csv | ./csv-row-indexer -o json
```

Perform column reordering based on a given sequence of column headers:

```
cat csv/file.csv | ./csv-column-reorder -c occupation,password,name,birthdate -o json
```

Perform column removal by given name or index:

```
cat csv/file.csv | ./csv-column-remove -c birthdate -o json
```

Truncate string column values to given length (with validation that the value is a string):

```
cat csv/file.csv | ./csv-column-truncate -c name -l 3 -o json
```

Reformat datetime column values using given format (with validation of source value to be valid datetime):

```
cat csv/file.csv | ./csv-date-reformat -c birthdate -f "l, d-M-Y H:i:s T" -o json
```

Encrypt/Decrypt values in certain column(s) with provided keys, using asymmetric encryption:

```

```

Join CSV files similarly to RDBMS inner join (join two CSVs based on equality of values in two related columns), output the joined CSV with all columns:

```

```

Select columns and apply where clauses (equality, less than, greater than, like regexp), output the CSV with selected columns and corresponding rows:

```

```

Translate value in given column from one language to another,given the destination language code:

```

```