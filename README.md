#ObjectOrder
Share items into groups by specifying former items.

[![Code Climate](https://codeclimate.com/github/trzczy/ObjectOrder/badges/gpa.svg)](https://codeclimate.com/github/trzczy/ObjectOrder)
[![Test Coverage](https://codeclimate.com/github/trzczy/ObjectOrder/badges/coverage.svg)](https://codeclimate.com/github/trzczy/ObjectOrder/coverage)
[![Issue Count](https://codeclimate.com/github/trzczy/ObjectOrder/badges/issue_count.svg)](https://codeclimate.com/github/trzczy/ObjectOrder)

### Example usage

    require_once __DIR__ . '/vendor/autoload.php';
    use Trzczy\Helpers\Rules;
    
    $jsonData = '[
            {
                "method":"Zbigniew",
                "input":"Herbert",
                "arg1":24,
                "arg2":"abc"
            },
            {
                "method":"Frank",
                "input":"Herbert",
                "former":[
                    {"arg2":"abc"}
                ]
            },
            {
                "method":"Edith",
                "input":"Stein",
                "former":[
                    {"method":"Frank"},
                    {"arg2":"abc"}
                ]
            },
            {
                "method":"Ernest",
                "input":"Hemingway",
                "former":[
                    {"input":"Herbert"},
                    {"method":"Edith"}
                ]
    
            }
        ]';
    $rules = new Rules();
    print_r($rules->order($jsonData));