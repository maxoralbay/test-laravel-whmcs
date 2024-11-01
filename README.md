### Install
```
docker-compose build
docker-compose up -d
sh shell.sh
php artisan key:generate
php artisan migrate
```

### Test and get example test data
```
php artisan test --filter=GenerateContentTest ## generate test data
php artisan test --filter=ReqProccessControllerTest::test_proccessReqTraffic ## test proccess traffic
```

### API
```
localhost:8001/api/getReqTraffic?chunk=10 ## chunk = 1 - 1000
localhost:8001/api/proccessReqTraffic ### load traffic
```