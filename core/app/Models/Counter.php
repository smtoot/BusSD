<?php

namespace App\Models;

/**
 * Counter Model - Backward Compatibility Alias
 * 
 * This class extends Branch to maintain backward compatibility  
 * with existing code that references Counter model.
 * 
 * @deprecated Use Branch model instead
 */
class Counter extends Branch
{
    protected $table = 'branches';
    
    // Inherits all methods and properties from Branch model
    // This allows existing code to continue working while we migrate
}

