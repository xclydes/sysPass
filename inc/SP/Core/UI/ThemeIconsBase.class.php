<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      http://syspass.org
 * @copyright 2012-2016 Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace SP\Core\UI;

use SP\Html\Assets\FontIcon;
use SP\Html\Assets\ImageIcon;

defined('APP_ROOT') || die(_('No es posible acceder directamente a este archivo'));

/**
 * Class ThemeIconsBase para la implementación de los iconos del tema visual
 *
 * @package SP\Core
 */
abstract class ThemeIconsBase implements ThemeIconsInterface
{
    /** @var  FontIcon|ImageIcon */
    protected $iconAdd;
    /** @var  FontIcon|ImageIcon */
    protected $iconView;
    /** @var  FontIcon|ImageIcon */
    protected $iconEdit;
    /** @var  FontIcon|ImageIcon */
    protected $iconDelete;
    /** @var  FontIcon|ImageIcon */
    protected $iconNavPrev;
    /** @var  FontIcon|ImageIcon */
    protected $iconNavNext;
    /** @var  FontIcon|ImageIcon */
    protected $iconNavFirst;
    /** @var  FontIcon|ImageIcon */
    protected $iconNavLast;
    /** @var  FontIcon|ImageIcon */
    protected $iconEditPass;
    /** @var  FontIcon|ImageIcon */
    protected $iconAppAdmin;
    /** @var  FontIcon|ImageIcon */
    protected $iconAccAdmin;
    /** @var  FontIcon|ImageIcon */
    protected $iconLdapUser;
    /** @var  FontIcon|ImageIcon */
    protected $iconDisabled;
    /** @var  FontIcon|ImageIcon */
    protected $iconViewPass;
    /** @var  FontIcon|ImageIcon */
    protected $iconCopy;
    /** @var  FontIcon|ImageIcon */
    protected $iconClipboard;
    /** @var  FontIcon|ImageIcon */
    protected $iconEmail;
    /** @var  FontIcon|ImageIcon */
    protected $iconOptional;
    /** @var  FontIcon|ImageIcon */
    protected $iconUp;
    /** @var  FontIcon|ImageIcon */
    protected $iconDown;
    /** @var  FontIcon|ImageIcon */
    protected $iconRefresh;
    /** @var  FontIcon|ImageIcon */
    protected $iconPublicLink;
    /** @var  FontIcon|ImageIcon */
    protected $iconBack;
    /** @var  FontIcon|ImageIcon */
    protected $iconRestore;
    /** @var  FontIcon|ImageIcon */
    protected $iconSave;
    /** @var  FontIcon|ImageIcon */
    protected $iconHelp;
    /** @var  FontIcon|ImageIcon */
    protected $iconClear;
    /** @var  FontIcon|ImageIcon */
    protected $iconPlay;
    /** @var  FontIcon|ImageIcon */
    protected $iconDownload;
    /** @var  FontIcon|ImageIcon */
    protected $iconWarning;
    /** @var  FontIcon|ImageIcon */
    protected $iconCheck;

    /**
     * Icons constructor.
     */
    public function __construct()
    {
        $this->setIcons();
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconWarning()
    {
        return $this->iconWarning;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconDownload()
    {
        return $this->iconDownload;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconClear()
    {
        return $this->iconClear;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconPlay()
    {
        return $this->iconPlay;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconHelp()
    {
        return $this->iconHelp;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconPublicLink()
    {
        return $this->iconPublicLink;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconBack()
    {
        return $this->iconBack;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconRestore()
    {
        return $this->iconRestore;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconSave()
    {
        return $this->iconSave;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconUp()
    {
        return $this->iconUp;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconDown()
    {
        return $this->iconDown;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconViewPass()
    {
        return $this->iconViewPass;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconCopy()
    {
        return $this->iconCopy;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconClipboard()
    {
        return $this->iconClipboard;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconEmail()
    {
        return $this->iconEmail;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconRefresh()
    {
        return $this->iconRefresh;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconEditPass()
    {
        return $this->iconEditPass;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconAppAdmin()
    {
        return $this->iconAppAdmin;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconAccAdmin()
    {
        return $this->iconAccAdmin;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconLdapUser()
    {
        return $this->iconLdapUser;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconDisabled()
    {
        return $this->iconDisabled;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconNavPrev()
    {
        return $this->iconNavPrev;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconNavNext()
    {
        return $this->iconNavNext;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconNavFirst()
    {
        return $this->iconNavFirst;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconNavLast()
    {
        return $this->iconNavLast;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconAdd()
    {
        return $this->iconAdd;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconView()
    {
        return $this->iconView;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconEdit()
    {
        return $this->iconEdit;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconDelete()
    {
        return $this->iconDelete;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconOptional()
    {
        return $this->iconOptional;
    }

    /**
     * @return FontIcon|ImageIcon
     */
    public function getIconCheck()
    {
        return $this->iconCheck;
    }
}