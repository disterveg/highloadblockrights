/**
 * Класс обработчик компонента
 */
class HighloadblockFieldRights {
  /**
   * Устанавливаем входные данные
   *
   * @param params
   */
  constructor(params) {
    this.tasksOptions = params.tasksOptions;
    this.parentAccessCodes = params.parentAccessCodes;
    this.selected = params.selected;
    this.fieldName = params.fieldName;
    this.table = BX(this.fieldName + '_table');
  }

  /**
   * Инициализируем обработчики
   */
  init()
  {
    this.table.closest('td').setAttribute('colspan', 2);
    BX.Access.Init({
      other: {
        disabled_cr: true
      }
    });

    BX.Access.SetSelected(this.selected, this.fieldName);

    BX.bindDelegate(this.table, 'click', { className: 'bx-action-href' }, BX.proxy(this.showForm, this));
    BX.bindDelegate(this.table, 'click', { className: 'access-delete' }, BX.proxy(this.deleteRow, this));
  }

  /**
   * Показать форму
   */
  showForm() {
    BX.Access.ShowForm({
      callback: (obSelected) => {
        for (const provider in obSelected) {
          if (obSelected.hasOwnProperty(provider)) {
            for (const id in obSelected[provider]) {
              if (obSelected[provider].hasOwnProperty(id)) {
                const cnt = this.table.rows.length;
                let lastName = this.fieldName + '[0][TASK_ID]';
                if (cnt > 2) {
                  const lastCol = this.table.rows[cnt - 2].cells[1];
                  const lastSelect = lastCol.getElementsByTagName('select');
                  if (lastSelect.length > 0) {
                    lastName = lastSelect[0].name;
                  }
                }

                const regex = new RegExp(this.fieldName + '\\[(\\d+)\\]');
                const match = lastName.match(regex);
                let index = Number(match[1]) + 1;
                this.selected[id] = true;

                const row = this.getTableRow(id, index, obSelected, provider);
                this.table.getElementsByTagName('tbody')[0].insertBefore(row, BX('add_button'));

                const parentRight = this.table.querySelector('.RIGHTS_row_for_' + id);
                if (parentRight) {
                  BX.addClass(parentRight, 'iblock-strike-out');
                }
              }
            }
          }
        }
      },
      bind: this.fieldName
    });
  }

  /**
   * Получить строку таблицы
   *
   * @param id
   * @param index
   * @param obSelected
   * @param provider
   * @returns {tr}
   */
  getTableRow(id, index, obSelected, provider)
  {
    const tableRow = BX.create('tr');
    const td1Element = BX.create('td', {
      attrs: { align: 'right' },
      text: `${BX.Access.GetProviderName(provider)} ${BX.util.htmlspecialchars(obSelected[provider][id].name)}:`
    });
    tableRow.appendChild(td1Element);

    const td2Element = BX.create('td', { attrs: { align: 'left' } });
    tableRow.appendChild(td2Element);

    const input1Element = BX.create('input', {
      attrs: {
        type: 'hidden',
        name: `${this.fieldName}[${index}][RIGHT_ID]`,
        value: ''
      }
    });
    td2Element.appendChild(input1Element);

    const input2Element = BX.create('input', {
      attrs: {
        type: 'hidden',
        name: `${this.fieldName}[${index}][ACCESS_CODE]`,
        value: `${id}`
      }
    });
    td2Element.appendChild(input2Element);

    const selectElement = BX.create('select', {props: {name: `${this.fieldName}[${index}][TASK_ID]`}});
    this.tasksOptions.forEach((option) => {
      const optionElement = BX.create('option', {
        props: {value: option.value},
        text: option.text
      });
      selectElement.appendChild(optionElement);
    });
    td2Element.appendChild(selectElement);

    const removeBtn = BX.create('a', {
      attrs: {
        href: 'javascript:void(0);',
        'data-id': id,
        class: 'access-delete'
      },
    });
    td2Element.appendChild(removeBtn);
    return tableRow;
  }

  /**
   * Удалить строку
   *
   * @param link
   */
  deleteRow(link) {
    const id = BX.data(link.target, 'id');
    this.selected[id] = false;
    const row = BX.findParent(link.target, {tag: 'tr'}, true);
    BX.hide(row);
    const rightId = row.querySelector('input').value;
    const input = BX.create('input', {props: {name: this.fieldName + '['+rightId+'][DEL]', type: 'hidden', value: 'Y'}});
    row.appendChild(input);

    const parentRight = this.table.querySelector('.RIGHTS_row_for_' + id);
    if (parentRight) {
      BX.removeClass(parentRight, 'iblock-strike-out');
    }
  }
}