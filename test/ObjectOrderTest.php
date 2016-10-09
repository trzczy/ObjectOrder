<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use PHPUnit\Framework\TestCase;
use Trzczy\Helpers\Rules;

class ObjectOrderTest extends TestCase
{
    private $rules;

    public function initialAndExpectedValues()
    {
        return [
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert",
        "former":[{"arg2":"abc"}]},
        
    {"method":"Edith","input":"Stein",
        "former":[{"method":"Frank"},{"arg2":"abc"}]},
        
    {"method":"Ernest","input":"Hemingway",
        "former":[{"input":"Herbert"},{"method":"Edith"}]}
                ]',
                '[
    [{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"}],
    
    [{"method":"Frank","input":"Herbert"}],
    
    [{"method":"Edith","input":"Stein"}],
    
    [{"method":"Ernest","input":"Hemingway"}]
]'
            ],
//001
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert"},
    
    {"method":"Edith","input":"Stein",
        "former":[{"method":"Frank"},{"arg2":"abc"}]},
        
    {"method":"Ernest","input":"Hemingway",
        "former":[{"input":"Herbert"},{"method":"Edith"}]}
]',
                '[
    [{"method":"Frank","input":"Herbert"},{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"}],
    
    [{"method":"Edith","input":"Stein"}],
    
    [{"method":"Ernest","input":"Hemingway"}]
]'
            ],
//002
            [
                '[
  {"method":"Edith","input":"Stein",
      "former":[{"method":"Frank"},{"arg2":"abc"}]},

  {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},

  {"method":"Frank","input":"Herbert"},

  {"method":"Ernest","input":"Hemingway",
      "former":[{"method":"Frank"}]}
                ]',
                '[

  [{"method":"Frank","input":"Herbert"},{"method":"Zbigniew","arg1":24,"arg2":"abc","input":"Herbert"}],
  
  [{"method":"Ernest","input":"Hemingway"}, {"method":"Edith","input":"Stein"}]

]'
            ]
            ,//003
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert"},
        
    {"method":"Edith","input":"Stein"},
        
    {"method":"Ernest","input":"Hemingway",
        "former":[{"input":"Herbert"},{"method":"Edith"}]}
]',
                '[
    [{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},{"method":"Frank","input":"Herbert"},{"method":"Edith","input":"Stein"}],
    
    [{"method":"Ernest","input":"Hemingway"}]
]'
            ]
            ,//004
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert"},
        
    {"method":"Edith","input":"Stein",
        "former":[{"input":"Herbert"}]},
        
    {"method":"Ernest","input":"Hemingway",
        "former":[{"input":"Herbert"}]}
]',
                '[
    [{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},{"method":"Frank","input":"Herbert"}],
    
    [{"method":"Edith","input":"Stein"},{"method":"Ernest","input":"Hemingway"}]
]'
            ]
            ,//005
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert"},
        
    {"method":"Edith","input":"Stein",
        "former":[{"input":"Herbert"}]},
        
    {"method":"Ernest","input":"Hemingway"}
                ]',
                '[
    [{"method":"Ernest","input":"Hemingway"},{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},{"method":"Frank","input":"Herbert"}],
    
    [{"method":"Edith","input":"Stein"}]
]'
            ]
            ,//006
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert",
        "former":[{"arg1":24}]},
        
    {"method":"Edith","input":"Stein"},
        
    {"method":"Ernest","input":"Hemingway"}
                ]',
                '[
    [{"method":"Ernest","input":"Hemingway"},{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},{"method":"Edith","input":"Stein"}],
    
    [{"method":"Frank","input":"Herbert"}]
]'
            ]
            ,//007
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert",
        "former":[{"arg1":24}]},
        
    {"method":"Edith","input":"Stein",
        "former":[{"arg1":24}]},
        
    {"method":"Ernest","input":"Hemingway",
        "former":[{"arg1":24}]}
]',
                '[
    [{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"}],
    
    [{"method":"Edith","input":"Stein"},{"method":"Ernest","input":"Hemingway"},{"method":"Frank","input":"Herbert"}]
]'
            ]
            ,//00x
            [
                '[
    {"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"},
    
    {"method":"Frank","input":"Herbert",
        "former":[{"arg2":"abc"}]},
        
    {"method":"Edith","input":"Stein",
        "former":[{"method":"Frank"},{"arg2":"abc"}]},
        
    {"method":"Ernest","input":"Hemingway",
        "former":[{"input":"Herbert"},{"method":"Edith"}]}
                ]',
                '[
    [{"method":"Zbigniew","input":"Herbert","arg1":24,"arg2":"abc"}],
    
    [{"method":"Frank","input":"Herbert"}],
    
    [{"method":"Edith","input":"Stein"}],
    
    [{"method":"Ernest","input":"Hemingway"}]
]'
            ]
        ];
    }

    /**
     * @dataProvider initialAndExpectedValues
     * @param $jsonData
     * @param $expected
     */
    public function testIfGivenElementsAreSortedAsExpected($jsonData, $expected)
    {
        $result = $this->rules->order($jsonData);
        $expected = json_decode($expected);
        array_map(function ($r, $e) {
//            $this->assertEquals($r, $e, "\$canonicalize = true", $delta = 0.0, $maxDepth = 10, $canonicalize = true);
            $this->assertTrue($this->arraysExtendedIdentity($e, $r));
        }, $result, $expected);
    }

    public function setUp()
    {
        $this->rules = new Rules();
    }

    private function arraysExtendedIdentity($array1, $array2)
    {
        {
            if (count($array1) !== count($array2)) {
                return false;
            }
            foreach ($array1 as $key => $value) {
                if (is_integer($key)) {
                    if (!in_array($value, $array2)) {
                        return false;
                    }
                } else {
                    if (!key_exists($key, $array2) or ($value !== $array2[$key])) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}