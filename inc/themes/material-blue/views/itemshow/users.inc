<?php
/** @var $icons \SP\Core\UI\ThemeIconsBase */
/** @var $user \SP\DataModel\UserData */
?>

<div id="box-popup">
    <h2 class="center"><?php echo $header; ?></h2>

    <form method="post" name="frmUsers" id="frmUsers" class="form-action"
          data-onsubmit="appMgmt/save"
          data-activetab="<?php echo isset($activeTab) ? $activeTab : ''; ?>"
          data-nextaction-id="<?php echo \SP\Core\ActionsInterface::ACTION_USR_USERS; ?>">
        <table class="popup-data">
            <tbody>
            <tr>
                <td class="descField"><?php echo _('Nombre') ?></td>
                <td class="valField">

                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <input id="name" name="name" type="text" required
                               class="mdl-textfield__input mdl-color-text--indigo-400"
                               value="<?php echo $user->getUserName(); ?>"
                               maxlength="80" <?php echo $isReadonly; ?>>
                        <label class="mdl-textfield__label"
                               for="name"><?php echo _('Nombre de usuario completo'); ?></label>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="descField"><?php echo _('Login'); ?></td>
                <td class="valField">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input id="login" name="login" type="text" required
                                   class="mdl-textfield__input mdl-color-text--indigo-400"
                                   value="<?php echo $user->getUserLogin(); ?>"
                                   maxlength="80" <?php echo $user->isUserIsLdap() || $isReadonly; ?>>
                            <label class="mdl-textfield__label"
                                   for="login"><?php echo _('Login de inicio de sesión'); ?></label>
                        </div>
                    <?php if ($user->isUserIsLdap()): ?>
                        <i class="material-icons <?php echo $icons->getIconLdapUser()->getClass(); ?>"
                           title="<?php echo $icons->getIconLdapUser()->getTitle(); ?>"><?php echo $icons->getIconLdapUser()->getIcon(); ?></i>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <td class="descField"><?php echo _('Perfil'); ?></td>
                <td class="valField">
                    <select id="selProfile" name="profileid"
                            class="select-box" required <?php echo $isDisabled; ?>>
                        <option value=""><?php echo _('Seleccionar Perfil'); ?></option>
                        <?php foreach ($profiles as $profile): ?>
                            <option
                                    value="<?php echo $profile->id; ?>" <?php echo ($profile->id == $user->getUserProfileId()) ? 'selected' : ''; ?>><?php echo $profile->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="descField"><?php echo _('Grupo'); ?></td>
                <td class="valField">
                    <select id="selGroup" name="groupid"
                            class="select-box" required <?php echo $isDisabled; ?>>
                        <option value=""><?php echo _('Seleccionar Grupo'); ?></option>
                        <?php foreach ($groups as $group): ?>
                            <option
                                    value="<?php echo $group->id; ?>" <?php echo ($group->id == $user->getUserGroupId()) ? 'selected' : ''; ?>><?php echo $group->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <td class="descField"><?php echo _('Email'); ?></td>
                <td class="valField">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input id="email" name="email" type="email" required
                                   class="mdl-textfield__input mdl-color-text--indigo-400"
                                   value="<?php echo $user->getUserEmail(); ?>" maxlength="50" <?php echo $isReadonly; ?>>
                            <label class="mdl-textfield__label"
                                   for="email"><?php echo _('Dirección de correo'); ?></label>
                        </div
                </td>

            </tr>

            <?php if ($actionId === \SP\Core\ActionsInterface::ACTION_USR_USERS_NEW): ?>
                <tr>
                    <td class="descField"><?php echo _('Clave'); ?></td>
                    <td class="valField">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input id="userpass" name="pass" type="password" required
                                   class="mdl-textfield__input passwordfield__input mdl-color-text--indigo-400"
                                   maxlength="50">
                            <label class="mdl-textfield__label"
                                   for="userpass"><?php echo _('Clave'); ?></label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="descField"><?php echo _('Clave (repetir)'); ?></td>
                    <td class="valField">
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                            <input id="userpassR" name="passR" type="password" required
                                   class="mdl-textfield__input mdl-color-text--indigo-400"
                                   maxlength="50">
                            <label class="mdl-textfield__label"
                                   for="userpassR"><?php echo _('Clave (repetir)'); ?></label>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <tr>
                <td class="descField"><?php echo _('Notas') ?></td>
                <td class="valField">
                    <?php if (!$isView): ?>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <textarea class="mdl-textfield__input" rows="3" id="notes" name="notes"
                                  maxlength="1000" <?php echo $isReadonly ;?>><?php echo $user->getUserNotes(); ?></textarea>
                            <label class="mdl-textfield__label"
                                   for="notes"><?php echo _('Notas sobre la cuenta'); ?></label>
                        </div>
                    <?php else: ?>
                        <?php echo $user->getUserNotes(); ?>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <td class="descField"><?php echo _('Opciones'); ?></td>
                <td class="valField checkbox">
                    <?php if ($SessionUserData->isUserIsAdminApp() || $isDemo): ?>
                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="adminapp"
                               title="<?php echo _('Administrador de la aplicación'); ?>">
                            <input type="checkbox" id="adminapp" class="mdl-switch__input mdl-color-text--indigo-400"
                                   name="adminapp" <?php echo $user->isUserIsAdminApp() ? 'checked' : ' '; ?> <?php echo $isDisabled; ?>/>
                            <span class="mdl-switch__label"><?php echo _('Admin. Aplicación'); ?></span>
                        </label>

                        <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="adminacc"
                               title="<?php echo _('Administrador de cuentas'); ?>">
                            <input type="checkbox" id="adminacc" class="mdl-switch__input mdl-color-text--indigo-400"
                                   name="adminacc" <?php echo $user->isUserIsAdminAcc() ? 'checked' : ' '; ?> <?php echo $isDisabled; ?>/>
                            <span class="mdl-switch__label"><?php echo _('Admin. Cuentas'); ?></span>
                        </label>
                    <?php endif; ?>
                    <br>
                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="disabled"
                           title="<?php echo _('Deshabilitado'); ?>">
                        <input type="checkbox" id="disabled" class="mdl-switch__input mdl-color-text--indigo-400"
                               name="disabled" <?php echo $user->isUserIsDisabled() ? 'checked' : ' '; ?> <?php echo $isDisabled; ?>/>
                        <span class="mdl-switch__label"><?php echo _('Deshabilitado'); ?></span>
                    </label>

                    <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="changepass"
                           title="<?php echo _('Forzar cambio de clave'); ?>">
                        <input type="checkbox" id="changepass" class="mdl-switch__input mdl-color-text--indigo-400"
                               name="changepass" <?php echo $user->isUserIsChangePass() ? 'checked' : ' '; ?> <?php echo $isDisabled; ?>/>
                        <span class="mdl-switch__label"><?php echo _('Cambio de Clave'); ?></span>
                    </label>
                </td>
            </tr>

            <?php if ($customFields): ?>
                <?php include $this->includeTemplate('aux-customfields', 'common'); ?>
            <?php endif; ?>

            <?php if ($isView): ?>
                <tr>
                    <td class="descField"><?php echo _('Entradas'); ?></td>
                    <td class="valField"> <?php echo $user->getUserCount(); ?></td>
                </tr>

                <tr>
                    <td class="descField"><?php echo _('Último Acceso'); ?></td>
                    <td class="valField"> <?php echo $user->getUserLastLogin(); ?></td>
                </tr>

                <tr>
                    <td class="descField"><?php echo _('Última Modificación'); ?></td>
                    <td class="valField"> <?php echo $user->getUserLastUpdate(); ?></td>
                </tr>

                <tr>
                    <td class="descField"><?php echo _('Fecha Clave Maestra'); ?></td>
                    <td class="valField"> <?php echo $user->getUserLastUpdateMPass(); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php if (!$isView): ?>
            <input type="hidden" name="isLdap" value="<?php echo $user->isUserIsLdap() ? 1 : 0; ?>"/>
            <input type="hidden" name="itemId" value="<?php echo $user->getUserId(); ?>"/>
            <input type="hidden" name="actionId" value="<?php echo $actionId; ?>"/>
            <input type="hidden" name="sk" value="<?php echo $sk; ?>">
            <input type="hidden" name="isAjax" value="1">
        <?php endif; ?>
    </form>

    <?php if (!$isView): ?>
        <div class="action-in-box">
            <button form="frmUsers"
                    class="mdl-button mdl-js-button mdl-button--fab mdl-button--mini-fab mdl-button--colored <?php echo $icons->getIconSave()->getClassButton(); ?>"
                    title="<?php echo $icons->getIconSave()->getTitle(); ?>">
                <i class="material-icons"><?php echo $icons->getIconSave()->getIcon(); ?></i>
            </button>
        </div>
    <?php endif; ?>
</div>
<script type="text/javascript">
    sysPassApp.sk.set("<?php echo $sk; ?>");
</script>