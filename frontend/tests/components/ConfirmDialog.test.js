import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ConfirmDialog from '../../src/components/ConfirmDialog.vue'
import BaseButton from '../../src/components/BaseButton.vue'

describe('ConfirmDialog', () => {
  const defaultProps = {
    show: true,
    title: 'Test Title',
    message: 'Test message',
  }

  const mountOptions = {
    global: {
      stubs: { teleport: true },
      components: { BaseButton }
    }
  }

  it('renders when show is true', () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      ...mountOptions
    })
    expect(wrapper.text()).toContain('Test Title')
    expect(wrapper.text()).toContain('Test message')
  })

  it('does not render when show is false', () => {
    const wrapper = mount(ConfirmDialog, {
      props: { ...defaultProps, show: false },
      ...mountOptions
    })
    expect(wrapper.find('.fixed').exists()).toBe(false)
  })

  it('renders default button texts', () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      ...mountOptions
    })
    expect(wrapper.text()).toContain('Cancelar')
    expect(wrapper.text()).toContain('Confirmar')
  })

  it('renders custom button texts', () => {
    const wrapper = mount(ConfirmDialog, {
      props: {
        ...defaultProps,
        confirmText: 'Excluir',
        cancelText: 'Voltar',
      },
      ...mountOptions
    })
    expect(wrapper.text()).toContain('Excluir')
    expect(wrapper.text()).toContain('Voltar')
  })

  it('emits confirm event when confirm button is clicked', async () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      ...mountOptions
    })
    // Find buttons with primary variant (confirm buttons)
    const buttons = wrapper.findAll('button')
    // The confirm buttons have bg-primary-500 class
    const confirmButton = buttons.find(b => b.classes().includes('bg-primary-500'))
    await confirmButton.trigger('click')
    expect(wrapper.emitted('confirm')).toBeTruthy()
  })

  it('emits cancel event when cancel button is clicked', async () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      ...mountOptions
    })
    // Find buttons with secondary variant (cancel buttons)
    const buttons = wrapper.findAll('button')
    const cancelButton = buttons.find(b => b.classes().includes('border-gray-300'))
    await cancelButton.trigger('click')
    expect(wrapper.emitted('cancel')).toBeTruthy()
  })

  it('emits cancel event when backdrop is clicked', async () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      ...mountOptions
    })
    await wrapper.find('.bg-black\\/50').trigger('click')
    expect(wrapper.emitted('cancel')).toBeTruthy()
  })

  it('applies danger styling when danger prop is true', () => {
    const wrapper = mount(ConfirmDialog, {
      props: { ...defaultProps, danger: true },
      ...mountOptions
    })
    const buttons = wrapper.findAll('button')
    const dangerButton = buttons.find(b => b.classes().includes('bg-red-500'))
    expect(dangerButton).toBeTruthy()
  })

  it('applies primary styling when danger prop is false', () => {
    const wrapper = mount(ConfirmDialog, {
      props: { ...defaultProps, danger: false },
      ...mountOptions
    })
    const buttons = wrapper.findAll('button')
    const primaryButton = buttons.find(b => b.classes().includes('bg-primary-500'))
    expect(primaryButton).toBeTruthy()
  })
})
