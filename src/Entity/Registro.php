<?php

namespace App\Entity;

use App\Repository\RegistroRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity(repositoryClass=RegistroRepository::class)
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Registro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apaterno;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $institucion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genero;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $correo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $porcentaje;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profesorInst;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profesorCorreo;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="emalca20_credencial", fileNameProperty="credencialName")
     *
     * @Assert\File(
     *     maxSize = "2M",
     * uploadFormSizeErrorMessage = "El archivo debe ser menor a 2 MB",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     *
     * @var File
     */
    public $credencialFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $credencialName;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="emalca20_historial", fileNameProperty="historialName")
     *
     * @Assert\File(
     *     maxSize = "2M",
     * uploadFormSizeErrorMessage = "El archivo debe ser menor a 2 MB",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     *
     * @var File
     */
    public $historialFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $historialName;

    /**
     * @Gedmo\Slug(fields={"nombre", "apaterno"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modifiedAt", type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pais;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $referencia;


    public function getSlug()
    {
        return $this->slug;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
{
    $this->nombre = $nombre;

    return $this;
}

    public function getApaterno(): ?string
    {
        return $this->apaterno;
    }

    public function setApaterno(string $apaterno): self
{
    $this->apaterno = $apaterno;

    return $this;
}

    public function getInstitucion(): ?string
    {
        return $this->institucion;
    }

    public function setInstitucion(string $institucion): self
{
    $this->institucion = $institucion;

    return $this;
}

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(string $genero): self
{
    $this->genero = $genero;

    return $this;
}

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
{
    $this->correo = $correo;

    return $this;
}

    public function getPorcentaje(): ?string
    {
        return $this->porcentaje;
    }

    public function setPorcentaje(string $porcentaje): self
{
    $this->porcentaje = $porcentaje;

    return $this;
}

    public function getProfesorInst(): ?string
    {
        return $this->profesorInst;
    }

    public function setProfesorInst(string $profesorInst): self
{
    $this->profesorInst = $profesorInst;

    return $this;
}

    public function getProfesorCorreo(): ?string
    {
        return $this->profesorCorreo;
    }

    public function setProfesorCorreo(string $profesorCorreo): self
{
    $this->profesorCorreo = $profesorCorreo;

    return $this;
}

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): self
{
    $this->pais = $pais;

    return $this;
}

public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    public function setReferencia(string $referencia): self
{
    $this->referencia = $referencia;

    return $this;
}


 /**
  * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
  * of 'UploadedFile' is injected into this setter to trigger the update. If this
  * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
  * must be able to accept an instance of 'File' as the bundle will inject one here
  * during Doctrine hydration.
  *
  * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
  */
    public function setCredencialFile(?File $credencialFile = null): void
    {
        $this->credencialFile = $credencialFile;

        if (null !== $credencialFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getCredencialFile(): ?File
    {
        return $this->credencialFile;
    }

  public function setCredencialName(?string $credencialName): void
    {
        $this->credencialName = $credencialName;
    }

    public function getCredencialName(): ?string
    {
        return $this->credencialName;
    }

/**
 * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
 * of 'UploadedFile' is injected into this setter to trigger the update. If this
 * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
 * must be able to accept an instance of 'File' as the bundle will inject one here
 * during Doctrine hydration.
 *
 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
 */
    public function setHistorialFile(?File $historialFile = null): void
    {
        $this->historialFile = $historialFile;

        if (null !== $historialFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getHistoriallFile(): ?File
    {
        return $this->historialFile;
    }

  public function setHistorialName(?string $historialName): void
    {
        $this->historialName = $historialName;
    }

    public function getHistorialName(): ?string
    {
        return $this->historialName;
    }

/**
 * Set createdAt
 *
 * @param \DateTime $createdAt
 *
 * @return Registro
 */
    public function setCreatedAt($createdAt)
{
    $this->createdAt = $createdAt;

    return $this;
}

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
{
    return $this->createdAt;
}

  /**
   * @return \DateTime
   */
    public function getModifiedAt()
{
    return $this->modifiedAt;
}

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
{
    $this->modifiedAt = $modifiedAt;
}
/**
 * @ORM\PrePersist
 */
    public function prePersist()
{
    $this->setCreatedAt(new \DateTime());
    $this->setModifiedAt(new \DateTime());
}


    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
{
    $this->setModifiedAt(new \DateTime());
}



/**
 * @Assert\IsTrue(message = "Si tu porcentaje de avance de licenciatura es menor al 100% es
   necesario que nos envies tu credencial que compruebe que actualmente estás inscrito."))
 */
    public function isPorcentajeValid()

{

    $porcentaje = $this->porcentaje;
    $credencial = $this->credencialFile;

    if ($porcentaje >= 50  && $credencial == null)
    {
        return false;
    }
    elseif ( $porcentaje == 0 )
        return true;
    }


}

