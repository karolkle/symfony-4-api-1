<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Controller\ResetPasswordController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *          },
 *          "post"
 *       },
 *      itemOperations={
 *         "get"={
 *             "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *         },
 *          "put"={
 *             "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *
 *          },
 *          "put-reset-password"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "path"="/users/{id}/reset-password",
 *              "method"="PUT",
 *              "controller"=ResetPasswordController::class,
 *              "denormalization_context"={
 *                  "groups"={"put-reset-password"}
 *               }
 *           }
 *     }
 *
 * )
 * @UniqueEntity("username", groups={"post"})
 * @UniqueEntity("email", groups={"post"})
 */
class User implements UserInterface
{

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_USER];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get-owner"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Email(groups={"post", "put"})
     * @Assert\Length(min=6, max=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"post", "get", "get-owner"})
     * @Assert\Length(min=6, max=255, groups={"post"})

     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post"})
     * @Assert\NotBlank(groups={"post"})
     * @Assert\Regex(
     *     pattern="?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     groups={"post"},
     *     message="Password is wrong"
     * )
     */
    private $password;

    /**

     * @Assert\NotBlank(groups={"post"})
     * @Groups({"post"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getPasswodAgain()",
     *     groups={"post"},
     *     message="Password is wrong"
     * )
     */
    private $passwordAgain;



    /**

     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset_password"})
     * @Assert\Regex(
     *     pattern="?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     groups={"put-reset-password"},
     *     message="Password is wrong"
     * )
     */
    private $newPassword;

    /**
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Groups({"put-reset-password"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getNewPasswodAgain()",
     *     groups={"put-reset-password"},
     *     message="Password is wrong"
     * )
     */

    private $newPasswordAgain;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @UserPassword(groups={"put-reset-password"})
     */

    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "put-test", "get-all-competition-with-people", "get-owner"})
     * @Assert\NotBlank
     */

    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"get", "post", "put", "get-owner"})
     * @Assert\Country
     */

    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Country
     * @Groups({"get", "post", "put", "get-owner"})
     */

    private $nationality;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=9, max=9)
     * @Groups({"get", "post", "put", "get-owner"})
     */

    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     * @Groups({"get", "post", "put", "get-all-competition-with-people", "get-owner"})
     */

    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     * @Groups({"get", "post", "put", "get-all-competition-with-people", "get-owner"})
     */

    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Choice({"male", "female"})
     * @Groups({"get", "post", "put", "get-all-competition-with-people", "get-owner"})
     */

    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Date
     * @Assert\NotBlank
     * @Groups({"get", "post", "put", "get-all-competition-with-people", "get-owner"})
     */

    private $dateOfBirth;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AllCompetition", inversedBy="peoples")
     * @Groups({"get"})
     * @ApiSubresource
     */
    private $participation;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"get-admin"})
     */
    private $roles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;
    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $confirmationToken;

    public function __construct()
    {

        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
        $this->confirmationToken = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getPasswordAgain()
    {
        return $this->passwordAgain;
    }


    public function setPasswordAgain($passwordAgain): void
    {
        $this->passwordAgain = $passwordAgain;
    }


    public function getCity(): ?string
    {
        return $this->city;
    }


    public function setCity($city): self
    {
        $this->city = $city;
        return $this;
    }


    public function getCountry(): ?string
    {
        return $this->country;
    }


    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }


    public function getNationality(): ?string
    {
        return $this->nationality;
    }


    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;
        return $this;
    }


    public function getPhone(): ?string
    {
        return $this->phone;

    }


    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }


    public function getFirstName(): ?string
    {
        return $this->firstName;
    }


    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }


    public function getLastName(): ?string
    {
        return $this->lastName;
    }


    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }


    public function getGender()
    {
        return $this->gender;
    }


    public function setGender($gender): void
    {
        $this->gender = $gender;
    }


    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }


    public function setDateOfBirth($dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return Collection
     */
    public function getParticipation()
    {
        return $this->participation;
    }

    public function setParticipation($participation): void
    {
        $this->participation = $participation;
    }


    public function __toString(): string{
        return $this->email;
    }


    public function getUsername(): ?string
    {
        return $this->username;

    }


    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }


    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }


    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }


    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }


    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return null
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param null $confirmationToken
     */
    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPasswordAgain()
    {
        return $this->newPasswordAgain;
    }


    public function setNewPasswordAgain($newPasswordAgain): void
    {
        $this->newPasswordAgain = $newPasswordAgain;
    }


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }


    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }



}
