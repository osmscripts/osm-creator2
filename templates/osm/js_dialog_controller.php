<?php
/* @var string $class Class name */
?>
import CancelModalDialog from "Osm_Ui_Dialogs/CancelModalDialog";
import macaw from "Osm_Framework_Js/vars/macaw";
import Form from "Osm_Ui_Forms/Form";

export default class <?php echo $class ?> extends CancelModalDialog {
    get events() {
        return Object.assign({}, super.events, {
            'click &___footer__ok': 'onOk',
            'form:success &__form': 'onFormSuccess',
        });
    }

    get form() {
        return macaw.get(document.getElementById(this.getAliasedId('&__form')), Form);
    }

    onOk() {
        this.form.submit();
    }

    onFormSuccess(e) {
        this.resolve(JSON.parse(e.detail));
    }
};