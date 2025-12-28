import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ConfirmDialog from '../../src/components/ConfirmDialog.vue'

describe('ConfirmDialog', () => {
  const defaultProps = {
    show: true,
    title: 'Test Title',
    message: 'Test message',
  }

  it('renders when show is true', () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.text()).toContain('Test Title')
    expect(wrapper.text()).toContain('Test message')
  })

  it('does not render when show is false', () => {
    const wrapper = mount(ConfirmDialog, {
      props: { ...defaultProps, show: false },
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.find('.fixed').exists()).toBe(false)
  })

  it('renders default button texts', () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.text()).toContain('Cancel')
    expect(wrapper.text()).toContain('Confirm')
  })

  it('renders custom button texts', () => {
    const wrapper = mount(ConfirmDialog, {
      props: {
        ...defaultProps,
        confirmText: 'Delete',
        cancelText: 'Go Back',
      },
      global: {
        stubs: { teleport: true }
      }
    })
    expect(wrapper.text()).toContain('Delete')
    expect(wrapper.text()).toContain('Go Back')
  })

  it('emits confirm event when confirm button is clicked', async () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      global: {
        stubs: { teleport: true }
      }
    })
    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click') // Second button is confirm
    expect(wrapper.emitted('confirm')).toBeTruthy()
  })

  it('emits cancel event when cancel button is clicked', async () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      global: {
        stubs: { teleport: true }
      }
    })
    const buttons = wrapper.findAll('button')
    await buttons[0].trigger('click') // First button is cancel
    expect(wrapper.emitted('cancel')).toBeTruthy()
  })

  it('emits cancel event when backdrop is clicked', async () => {
    const wrapper = mount(ConfirmDialog, {
      props: defaultProps,
      global: {
        stubs: { teleport: true }
      }
    })
    await wrapper.find('.bg-black\\/50').trigger('click')
    expect(wrapper.emitted('cancel')).toBeTruthy()
  })

  it('applies danger styling when danger prop is true', () => {
    const wrapper = mount(ConfirmDialog, {
      props: { ...defaultProps, danger: true },
      global: {
        stubs: { teleport: true }
      }
    })
    const confirmButton = wrapper.findAll('button')[1]
    expect(confirmButton.classes()).toContain('bg-red-500')
  })

  it('applies primary styling when danger prop is false', () => {
    const wrapper = mount(ConfirmDialog, {
      props: { ...defaultProps, danger: false },
      global: {
        stubs: { teleport: true }
      }
    })
    const confirmButton = wrapper.findAll('button')[1]
    expect(confirmButton.classes()).toContain('bg-primary-500')
  })
})
