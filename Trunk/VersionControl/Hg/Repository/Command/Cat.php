<?php



class Cat extends RepositoryCommand
{
    /**
         * The file to be operated upon with relation to the repository.
         *
         * @var string
         */
        private $_file;

    /**
     * Set the specific file within the repository (or, working copy??) to operated upon
     *
     * @return Repository
     * @see $_file
     */
    public function getFile( $file )
    {
        //should raise exception if the file is not versioncontrolled?
        //@todo How to find out? Run Status.php and ensure $file is in_array()?

        $this->_file = $file; //will be overwritten each time function is called.
        return $this;
        }

}

?>