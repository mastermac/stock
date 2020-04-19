Legend:

'-->' means 'required'

All free and pro files require files from 'core' catalog

'none' means 'this component doesn't require anything except core files'

A file wrapped by `< >` means that this file make the base component prettier but it isn't necessary for the proper working

All PRO components require 'pro/_variables.scss' file

scss/
|
|-- core/
|   |
|   |-- bootstrap/
|   |	|-- _functions.scss
|   |	|-- _variables.scss
|   |
|   |-- _colors.scss
|   |-- _global.scss
|   |-- _helpers.scss
|   |-- _masks.scss
|   |-- _mixins.scss
|   |-- _typography.scss
|   |-- _variables.scss
|   |-- _waves.scss
|
|-- free/
|   |-- _animations-basic.scss --> none
|   |-- _animations-extended.scss --> _animations-basic.scss
|   |-- _buttons.scss --> none
|   |-- _cards.scss --> none <_buttons.scss>
|   |-- _dropdowns.scss --> none <_buttons.scss>
|   |-- _input-group.scss --> _forms.scss, _buttons.scss, _dropdowns.scss
|   |-- _navbars.scss --> none <_buttons.scss, _forms.scss, _input-group.scss>
|   |-- _pagination.scss --> none
|   |-- _badges.scss --> none
|   |-- _modals.scss --> _buttons.scss, _forms.scss (PRO --> _tabs.scss)
|   |-- _carousels.scss --> <_buttons.scss>
|   |-- _forms.scss --> none
|   |-- _msc.scss --> none <_buttons.scss, _forms.scss, _cards.scss>
|   |-- _footers.scss none <_buttons.scss> (PRO: )
|   |-- _list-group.scss --> none
|   |-- _tables.scss --> none (PRO: _material-select.scss, pro/_forms.scss, _checkbox.scss, pro/_buttons.scss, pro/_cards.scss, _pagination.scss, pro/_msc.scss)
|   |-- _depreciated.scss
|
|-- pro/
|   |
|   |-- picker/
|   |   |-- _default.scss --> none
|   |   |-- _default-time.scss --> _default.scss, free/_forms.scss, free/_buttons.scss, pro/_buttons.scss, free/_cards.scss
|   |   |-- _default-date.scss --> _default.scss, free/_forms.scss
|   |
|   |-- sections/
|   |   |-- _templates.scss --> _sidenav.scss
|   |   |-- _social.scss --> free/_cards.scss, free/ _forms.scss, free/_buttons.scss, pro/_buttons.scss,
|   |   |-- _team.scss --> free/_buttons.scss, pro/_buttons.scss, free/_cards.scss, pro/_cards.scss
|   |   |-- _testimonials.scss --> free/_carousels.scss, pro/_carousels.scss, free/_buttons.scss, pro/_buttons.scss
|   |   |-- _magazine.scss --> _badges.scss
|   |   |-- _pricing.scss --> free/_buttons.scss, pro/_buttons.scss
|   |   |-- _contacts.scss --> free/_forms.scss, pro/_forms.scss, free/_buttons.scss, pro/_buttons.scss
|   |
|   |-- _variables.scss
|   |-- _buttons.scss --> free/_buttons.scss, pro/_msc.scss, _checkbox.scss, _radio.scss
|   |-- _social-buttons.scss --> free/_buttons.scss, pro/_buttons.scss
|   |-- _tabs.scss --> _cards.scss
|   |-- _cards.scss --> free/_cards.scss <_buttons.scss, _social-buttons.scss>
|   |-- _dropdowns.scss --> free/_dropdowns.scss, free/_buttons.scss
|   |-- _navbars.scss --> free/_navbars.scss  (PRO: )
|   |-- _scrollspy.scss --> none
|   |-- _lightbox.scss --> none
|   |-- _chips.scss --> none
|   |-- _msc.scss --> none
|   |-- _forms.scss --> none
|   |-- _radio.scss --> none
|   |-- _checkbox.scss --> none
|   |-- _material-select.scss --> none
|   |-- _switch.scss --> none
|   |-- _file-input.scss --> free/_forms.scss, free/_buttons.scss
|   |-- _range.scss --> none
|   |-- _input-group.scss --> free/_input-group.scss and the same what free input group, _checkbox.scss, _radio.scss
|   |-- _autocomplete.scss --> free/_forms.scss
|   |-- _accordion.scss --> pro/_animations.scss, free/_cards.scss
|   |-- _parallax.scss --> none
|   |-- _sidenav.scss --> free/_forms.scss, pro/_animations.scss, sections/_templates.scss
|   |-- _ecommerce.scss --> free/_cards.scss, pro/_cards.scss, free/_buttons.scss, pro/_buttons.scss, pro/_msc.scss
|   |-- _carousels.scss --> free/_carousels.scss, free/_cards.scss, free/_buttons.scss
|   |-- _steppers.scss --> free/_buttons.scss
|   |-- _blog.scss --> none
|   |-- _toasts.scss --> free/_buttons.scss
|   |-- _animations.scss --> none
|   |-- _charts.scss --> none
|   |-- _progress.scss --> none
|   |-- _scrollbar.scss --> none
|   |-- _skins.scss --> none
|   |-- _depreciated.scss
|
`-- _custom-skin.scss
`-- _custom-styles.scss
`-- _custom-variables.scss
`-- mdb.scss