# Database
CakePHP 3 plugin that provides classes for adding default validation rules from the database table schema and cleaning data being saved.

Caching is enabled by default but can be disabled in the configuration or at run-time.

## Main classes

AutovalidateBehavior reads various informations from the database table schema, such as column type, the fact that the column can be NULL, foreign key constraints, unique constraints and automatically adds those validation rules to the default ones.

FormattableBehavior cleans up data before validation or saving using configurable static class methods. The columns on which the formatters are applied can be defined based on column type and field name regular expression.

## Example usage

Add the following code to your table classes; default values are explicited here and not needed, unless you want to override them.

    public function initialize(array $config)
    {
        // ...
            $this->addBehavior('DatabaseAutovalidate',
                [
                    'className' => 'Database.Autovalidate',
                    // Default values
                    'accepted' => null,
                    'cache' => null,
                    'domain' => 'database'
                ]
            );

            $this->addBehavior('DatabaseFormattable',
                [
                    'className' => 'Database.Formattable',
                    // Default values
                    'cache' => null,
                    'formatters' => [
                        '\\Database\\Utility\\Formatter::suffix' => '/_id$/',
                        '\\Database\\Utility\\Formatter::trim' => [ 'NOT' => 'binary'],
                        '\\Database\\Utility\\Formatter::null' => true,
                        '\\Database\\Utility\\Formatter::integer' => ['integer', 'biginteger'],
                        '\\Database\\Utility\\Formatter::decimal' => ['decimal', 'float', 'numeric']
                    ]
                ]
            );
        // ...
    }