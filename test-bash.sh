echo "================================"
echo "    Testing Race Condition      "
echo "================================"
echo "Virtual Users: 10"
echo "Iterations: 1"
echo "================================"
echo "Starting test..."
php artisan migrate:fresh --seed
echo "Database migrated and seeded."
echo "Starting k6 load test..."
k6 run tests/Load/race-condition.js
echo "Load test completed."