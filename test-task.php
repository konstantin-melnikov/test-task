<?php
if (version_compare(PHP_VERSION, '7.4.0') <= 0) {
    throw new Exception('Minimal requirements version of PHP is 7.4.0', 0);
    exit;
}
/*
General requirements:
- implement the task as good as you can


Implement a Person class.

Person has following attributes:
- unique integer ID
- name
- surname
- sex M/F
- birth date 
You can get these information from the instance but you can not change them. (we do not consider ability to change name or sex)

Operations:
- Get person age in days.

*/

class Person
{
    private int $_id;
    private string $_name;
    private string $_surname;
    private bool $_isMale;
    private DateTime $_birthday;

    public function __construct(int $id, string $name, string $surname, bool $isMale, DateTime $birthday)
    {
        $this->_id       = $id;
        $this->_name     = $name;
        $this->_surname  = $surname;
        $this->_isMale   = $isMale;
        $this->_birthday = $birthday;
    }

    public function getId(): int
    {
        return $this->_id;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getSurname(): string
    {
        return $this->_surname;
    }

    public function isMale(): bool
    {
        return $this->_isMale;
    }

    public function getBirthday(): DateTime
    {
        return $this->_birthday;
    }

    public function getAgeInDays(): int
    {
        $now = new DateTime();
        $interval = $this->_birthday->diff($now);
        return (int) $interval->format('%a');
    }
}

/*
Implement Mankind class, which works with Person instances.

General requirements:
- there can only exist a single instance of the class (Martians are not mankind...)
- allow to use the instance as array (use person IDs as array keys) and allow to loop through the instance via foreach

Required operations:
- Load people from the file (see below)
- Get the Person based on ID
- get the percentage of Men in Mankind



Loading people from the file:

Input file is in CSV format. Each person is in separate line. 
Each line contains ID of the person, name, surname, sex (M/F) and birth date in format dd.mm.yyyy.
Attributes are separated by semicolon (;) File is using UTF8 encoding. 
 
Example:
123;Michal;Walker;M;01.11.1962
3457;Pavla;Nowak;F;13.04.1887

Expected number of records in the file <= 1000.

Also suggest how to handle the situation when the file is much larger (100 MiB+) - in terms of this method and the Mankind class itself.


*/

class Mankind implements Iterator, Countable
{
    private static Mankind $_instance;
    private array $_persons = [];

    private function __construct()
    {

    }

    public function rewind()
    {
        return reset($this->_persons);
    }

    public function current()
    {
        return current($this->_persons);
    }

    public function key()
    {
        return key($this->_persons);
    }

    public function next()
    {
        return next($this->_persons);
    }

    public function valid()
    {
        return key($this->_persons) !== null;
    }

    public function count()
    {
        return count($this->_persons);
    }

    public static function getInstance(): Mankind
    {
        if (empty(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    public function loadPersons(string $fileName): void
    {
        $f = fopen($fileName, 'r');
        while (($row = fgetcsv($f)) !== false) {
            $this->_persons[$row[0]] = new Person(
                $row[0],
                $row[1],
                $row[2],
                ($row[3] == 'M'),
                DateTime::createFromFormat('d.m.Y', $row[4])
            );
        }
    }

    public function getPerson(int $id): ?Person
    {
        return $this->_persons[$id] ?? null;
    }

    public function getPercenOfMen(): float
    {
        $men = 0;
        foreach ($this as $key => $person) {
            if ($person->isMale()) {
                $men++;
            }
        }
        return (count($this) > 0) ? $men / count($this) * 100 : 0;
    }
}

$humality = Mankind::getInstance();
$humality->loadPersons('humality.csv');

$person = $humality->getPerson(300);
var_dump($person);

var_dump($humality->getPercenOfMen());