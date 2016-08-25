<?php
abstract class AGame{
    
    abstract public function renderMenu();
    
    abstract public function setupGame($setup = array());
    
    abstract public function renderGame();
    
    abstract public function renderAdmin();
}