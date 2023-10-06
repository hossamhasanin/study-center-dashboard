<?php

namespace App\Models;

enum UserTypes: int
{
    case Admin = 0;
    case Teacher = 1;
    case Student = 2;

    public static function isAdmin(int $userType) : bool
    {
        return $userType == UserTypes::Admin->value;
    }

    public static function isTeacherOrAdmin(int $userType) : bool
    {
        return $userType == UserTypes::Teacher->value || self::isAdmin($userType);
    }

    public static function isTeacherOnly(int $userType) : bool
    {
        return $userType == UserTypes::Teacher->value;
    }

    public static function isStudentOrAdmin(int $userType) : bool
    {
        return $userType == UserTypes::Student->value || self::isAdmin($userType);
    }

    public static function isStudentOnly(int $userType) : bool
    {
        return $userType == UserTypes::Student->value;
    }
}
