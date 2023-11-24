<?php

namespace App\DataFixtures;

use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //! Add 10 fake of rows in students table database  
        for ( $i = 0 ; $i < 10 ; $i++ ) { 
            $student = new Student() ;

            //! Add fake data
            $student -> setFirstName("student first " . $i) ;
            $student -> setLastName("student last -". $i) ;
            $student -> setAge($i + 10) ;

            $manager->persist($student);
        }

        $manager->flush();
    }
}
