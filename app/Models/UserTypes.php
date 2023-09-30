<?php

namespace App\Models;

enum UserTypes: int
{
    case Admin = 1;
    case Teacher = 2;
    case Student = 3;

    public static function isAdmin(int $userType) : bool
    {
        return $userType == UserTypes::Admin;
    }

    public static function isTeacher(int $userType) : bool
    {
        return $userType == UserTypes::Teacher;
    }

    public static function isStudent(int $userType) : bool
    {
        return $userType == UserTypes::Student;
    }
}
