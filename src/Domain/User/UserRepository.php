<?php
declare (strict_types = 1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @param int $intUserTypeId
     * @param int $intRefrenceId
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserInfoByUserTypeIdByRefrenceId(int $intUserTypeId, int $intRefrenceId);

    /**
     * @param string $strUsername
     * @return User
     * @throws UserNotFoundException
     */
    public function fetchUserByUsername(string $strUsername): User;

    /**
     * @param  object $objPaginationParams
     * @return User[]
     * @throws UserNotFoundException
     */
    public function findAllUsersByPageNumberByLimit($objPaginationParams): array;

    /**
     * @param string $emailAddress
     * @return User
     * @throws UserNotFoundException
     */
    public function fetchUserByEmailAddress(string $emailAddress): array;

    /**
     * @param string $mobileNumber
     * @return User
     * @throws UserNotFoundException
     */
    public function fetchUserByMobileNumber(string $mobileNumber): array;
}